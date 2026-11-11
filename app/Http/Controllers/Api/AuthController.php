<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdvocateRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Resources\AdminResource;
use App\Http\Resources\LoginLogResource;
use App\Models\LoginLog;
use App\Models\User;
use App\Models\UserProfile;
use App\Services\AdvocateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * POST /api/auth/login
     * body: email, password
     *
     * Ek hi login sabke liye – role database se detect hota hai:
     *   superadmin -> /superadmin   admin (advocate) -> /admin   user -> /dashboard
     * Har attempt login_logs table me save hota hai.
     */
    public function login(Request $request): JsonResponse
    {
        $request->merge(['email' => strtolower(trim((string) $request->email))]);

        $data = $request->validate([
            'email'    => ['required', 'email', 'max:150'],
            'password' => ['required', 'string'],
        ], [
            'email.required'    => 'Please enter your email address.',
            'email.email'       => 'Please enter a valid email address.',
            'password.required' => 'Please enter your password.',
        ]);

        $login = $data['email'];
        $user  = User::whereRaw('LOWER(email) = ?', [$login])->first();

        // Password DB me plain text save ho gaya ho (bina hash) to bhi match karo aur turant hash kar do
        $passwordOk = false;
        if ($user && $user->password) {
            $stored = $user->getRawOriginal('password');
            if (Hash::isHashed($stored)) {
                $passwordOk = Hash::check($data['password'], $stored);
            } elseif (hash_equals((string) $stored, $data['password'])) {
                $passwordOk = true;
                $user->forceFill(['password' => Hash::make($data['password'])])->saveQuietly();
            }
        }

        // 1. Galat email ya password
        if (! $user || ! $passwordOk) {
            $reason = ! $user ? 'Email not found' : 'Wrong password';
            LoginLog::record($request, $user, 'failed', $reason, null, $login);

            // local / debug mode me exact wajah dikhao (production me common message)
            $msg = config('app.debug')
                ? (! $user ? 'No account found with this email. Run: php artisan superadmin:create' : 'Incorrect password for this email.')
                : 'Invalid email or password.';

            return response()->json([
                'message' => $msg,
                'errors'  => [(! $user ? 'email' : 'password') => [$msg]],
            ], 422);
        }

        // 2. Unknown role
        if (! in_array($user->role, User::ROLES, true)) {
            LoginLog::record($request, $user, 'blocked', 'Unknown role', null, $login);

            return response()->json(['message' => 'Your account role is not allowed to log in.'], 403);
        }

        // 3. Status check
        if ($user->status === 'pending') {
            LoginLog::record($request, $user, 'blocked', 'Pending approval', null, $login);

            return response()->json([
                'message' => 'Your registration is under review. You can log in once the superadmin approves your account.',
                'role'    => $user->role,
            ], 403);
        }

        if ($user->status !== 'active') {
            LoginLog::record($request, $user, 'blocked', 'Account inactive', null, $login);

            return response()->json([
                'message' => 'Your account has been deactivated. Please contact the superadmin.',
                'role'    => $user->role,
            ], 403);
        }

        // 4. Success – role ke saath token
        $newToken = $user->createToken('web-' . $user->role, ['role:' . $user->role]);
        $user->forceFill(['last_login_at' => now()])->save();
        LoginLog::record($request, $user, 'success', null, $newToken->accessToken->id, $login);

        return response()->json([
            'message'    => 'Login successful as ' . $user->roleLabel() . '.',
            'token'      => $newToken->plainTextToken,
            'token_type' => 'Bearer',
            'role'       => $user->role,
            'role_label' => $user->roleLabel(),
            'user'       => new AdminResource($user->load(['advocateProfile', 'userProfile', 'creator'])),
            'redirect'   => $user->dashboardUrl(),
        ]);
    }

    /**
     * POST /api/auth/register  (multipart/form-data)
     * Advocate registration – role "admin"
     * auto_approve = true  -> status "active", apni email + password se turant login
     * auto_approve = false -> status "pending", superadmin approve karega
     */
    public function register(AdvocateRequest $request, AdvocateService $service): JsonResponse
    {
        $autoApprove = (bool) config('advocate.auto_approve', true);
        $user = $service->save($request, $request->validated(), null, $autoApprove ? 'active' : 'pending');

        return response()->json([
            'message'   => $autoApprove
                ? 'Registration successful! You can now log in with your email (' . $user->email . ') and password.'
                : 'Registration submitted successfully! You will be able to log in once the superadmin verifies and approves your account.',
            'can_login' => $autoApprove,
            'login_url' => route('login'),
            'user'      => ['id' => $user->id, 'name' => $user->name, 'email' => $user->email, 'role' => $user->role, 'status' => $user->status],
        ], 201);
    }

    /**
     * POST /api/auth/user-register  (multipart/form-data)
     * Website SIGN UP – role "admin". Token nahi milta – login page par jaakar login karna hoga
     * users + user_profiles dono table me save hota hai.
     */
    public function userRegister(UserRegisterRequest $request): JsonResponse
    {
        $data = $request->validated();

        $user = DB::transaction(function () use ($request, $data) {
            $user = User::create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'phone'    => $data['phone'],
                'password' => $data['password'],          // 'hashed' cast
                'role'     => User::ROLE_ADMIN,                                   // signup = Admin (Advocate)
                'status'   => config('advocate.auto_approve', true) ? 'active' : 'pending',
                'photo'    => $request->hasFile('photo') ? $request->file('photo')->store('users/photos', 'public') : null,
            ]);

            $user->userProfile()->create(Arr::only($data, UserProfile::FIELDS));

            return $user;
        });

        // Sign Up ke baad auto-login NAHI – user login page par email + password se login karega
        $pending = $user->status !== 'active';

        return response()->json([
            'message'   => $pending
                ? 'Account created! Your admin account is pending approval. You can log in once the superadmin approves it.'
                : 'Admin account created successfully! Please log in with your email and password.',
            'pending'   => $pending,
            'role'      => $user->role,
            'email'     => $user->email,
            'login_url' => route('login') . '?email=' . urlencode($user->email) . '&registered=1',
        ], 201);
    }

    /** GET /api/auth/me */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user()->load(['advocateProfile', 'userProfile', 'creator']);

        return response()->json([
            'user'     => new AdminResource($user),
            'role'     => $user->role,
            'redirect' => $user->dashboardUrl(),
        ]);
    }

    /** GET /api/auth/login-history – apni last 10 logins */
    public function loginHistory(Request $request)
    {
        $logs = $request->user()->loginLogs()->latest('logged_in_at')->limit(10)->get();

        return LoginLogResource::collection($logs);
    }

    /** POST /api/auth/logout – current token delete + logout time save */
    public function logout(Request $request): JsonResponse
    {
        $token = $request->user()->currentAccessToken();

        if ($token && isset($token->id)) {
            LoginLog::where('token_id', $token->id)->whereNull('logged_out_at')->update(['logged_out_at' => now()]);
        }
        if ($token && method_exists($token, 'delete')) {
            $token->delete();
        }

        return response()->json(['message' => 'Logged out successfully.']);
    }
}