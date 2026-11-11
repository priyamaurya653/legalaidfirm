<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AdminResource;
use App\Http\Resources\LoginLogResource;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

/**
 * Superadmin only – SABHI users (superadmin + admin + user)
 * list / view / edit / status / delete
 */
class UserController extends Controller
{
    private const STATUSES = ['active', 'pending', 'inactive'];

    /** GET /api/users?search=&role=&status=&page=&per_page= */
    public function index(Request $request): AnonymousResourceCollection
    {
        $request->validate([
            'search'   => ['nullable', 'string', 'max:100'],
            'role'     => ['nullable', Rule::in(User::ROLES)],
            'status'   => ['nullable', Rule::in(self::STATUSES)],
            'per_page'   => ['nullable', 'integer', 'min:5', 'max:100'],
            'created_by' => ['nullable', 'integer'],     // 0 = kisi admin ka nahi
        ]);

        $users = User::query()
            ->with(['userProfile', 'creator:id,name,email,phone'])
            ->withCount(['loginLogs', 'createdUsers'])
            ->when($request->filled('created_by'), fn ($q) => $request->created_by === '0'
                ? $q->whereNull('created_by') : $q->where('created_by', $request->created_by))
            ->when($request->filled('role'), fn ($q) => $q->where('role', $request->role))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('search'), function ($q) use ($request) {
                $s = '%' . $request->search . '%';
                $q->where(fn ($w) => $w->where('name', 'like', $s)->orWhere('email', 'like', $s)->orWhere('phone', 'like', $s)
                    ->orWhereHas('userProfile', fn ($p) => $p->where('city', 'like', $s)));
            })
            ->latest()
            ->paginate($request->integer('per_page', 10))
            ->withQueryString();

        $roles  = User::selectRaw('role, COUNT(*) as total')->groupBy('role')->pluck('total', 'role');
        $status = User::selectRaw('status, COUNT(*) as total')->groupBy('status')->pluck('total', 'status');

        return AdminResource::collection($users)->additional([
            'stats' => [
                'total'      => (int) $roles->sum(),
                'superadmin' => (int) ($roles['superadmin'] ?? 0),
                'admin'      => (int) ($roles['admin'] ?? 0),
                'user'       => (int) ($roles['user'] ?? 0),
                'active'     => (int) ($status['active'] ?? 0),
                'inactive'   => (int) (($status['inactive'] ?? 0) + ($status['pending'] ?? 0)),
            ],
            'me'     => $request->user()->id,
            // "Created by admin" filter dropdown
            'admins' => User::where('role', User::ROLE_ADMIN)->orderBy('name')->get(['id', 'name']),
        ]);
    }

    /** GET /api/users/{user} – profile + last 10 logins */
    public function show(User $user): JsonResponse
    {
        $user->load(['userProfile', 'advocateProfile', 'creator:id,name,email,phone'])->loadCount(['loginLogs', 'createdUsers']);

        return response()->json([
            'data'   => new AdminResource($user),
            'logins' => LoginLogResource::collection($user->loginLogs()->latest('logged_in_at')->limit(10)->get()),
        ]);
    }

    /** POST /api/users/{user}  (multipart) – edit */
    public function update(Request $request, User $user): JsonResponse
    {
        $me     = $request->user();
        $isSelf = $me->id === $user->id;

        $request->merge([
            'email' => strtolower(trim((string) $request->email)),
            'phone' => preg_replace('/\D/', '', (string) $request->phone) ?: null,
        ]);

        $data = $request->validate([
            'name'      => ['required', 'string', 'min:3', 'max:100'],
            'email'     => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user->id)],
            'phone'     => ['nullable', 'digits:10', Rule::unique('users', 'phone')->ignore($user->id)],
            'role'      => ['required', Rule::in(User::ROLES)],
            'status'    => ['required', Rule::in(self::STATUSES)],
            'password'  => ['nullable', 'confirmed', Password::min(8)->letters()->mixedCase()->numbers()],
            'photo'     => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'gender'    => ['nullable', 'string', 'max:20'],
            'dob'       => ['nullable', 'date', 'before:today'],
            'address'   => ['nullable', 'string', 'max:500'],
            'city'      => ['nullable', 'string', 'max:80'],
            'state'     => ['nullable', 'string', 'max:60'],
            'pincode'   => ['nullable', 'digits:6'],
            'created_by' => ['nullable', 'integer', Rule::exists('users', 'id')->where('role', User::ROLE_ADMIN)],
        ], [
            'email.unique'       => 'This email is already used by another account.',
            'phone.unique'       => 'This phone number is already used by another account.',
            'password.confirmed' => 'Passwords do not match.',
        ]);

        // khud ka role / status nahi badal sakte (lock-out se bachne ke liye)
        if ($isSelf && ($data['role'] !== $user->role || $data['status'] !== 'active')) {
            return response()->json([
                'message' => 'You cannot change your own role or status.',
                'errors'  => ['role' => ['You cannot change your own role or status.']],
            ], 422);
        }
        // aakhri superadmin ka role na hate
        if ($user->role === User::ROLE_SUPERADMIN && $data['role'] !== User::ROLE_SUPERADMIN
            && User::where('role', User::ROLE_SUPERADMIN)->count() <= 1) {
            return response()->json(['message' => 'At least one superadmin is required.', 'errors' => ['role' => ['At least one superadmin is required.']]], 422);
        }

        DB::transaction(function () use ($request, $data, $user) {
            $user->forceFill(Arr::only($data, ['name', 'email', 'role', 'status']) + ['phone' => $data['phone'] ?? null]);
            // user ko kis admin ke under rakhna hai (sirf role = user)
            if ($request->has('created_by')) {
                $user->created_by = $data['role'] === User::ROLE_USER ? ($data['created_by'] ?? null) : null;
            }
            if (! empty($data['password'])) {
                $user->password = Hash::make($data['password']);
            }
            if ($request->hasFile('photo')) {
                if ($user->photo) Storage::disk('public')->delete($user->photo);
                $user->photo = $request->file('photo')->store('users/photos', 'public');
            }
            $user->save();

            $profile = Arr::only($data, ['gender', 'dob', 'address', 'city', 'state', 'pincode']);
            $user->userProfile()->updateOrCreate(['user_id' => $user->id], Arr::only($profile, UserProfile::FIELDS));
        });

        // inactive / password badla -> uske sab devices se logout
        if ($user->status !== 'active' || (! empty($data['password']) && ! $isSelf)) {
            $user->tokens()->delete();
        }

        return response()->json([
            'message' => 'User "' . $user->name . '" updated successfully.',
            'data'    => new AdminResource($user->fresh()->load(['userProfile', 'creator:id,name,email,phone'])->loadCount('loginLogs')),
        ]);
    }

    /** PATCH /api/users/{user}/status   body: { status } */
    public function updateStatus(Request $request, User $user): JsonResponse
    {
        abort_if($request->user()->id === $user->id, 422, 'You cannot change your own status.');

        $data = $request->validate(['status' => ['required', Rule::in(self::STATUSES)]]);
        $user->update(['status' => $data['status']]);

        if ($data['status'] !== 'active') {
            $user->tokens()->delete();
        }

        return response()->json([
            'message' => $data['status'] === 'active' ? 'User activated successfully.' : 'User deactivated successfully.',
        ]);
    }

    /** DELETE /api/users/{user} */
    public function destroy(Request $request, User $user): JsonResponse
    {
        abort_if($request->user()->id === $user->id, 422, 'You cannot delete your own account.');
        abort_if($user->role === User::ROLE_SUPERADMIN && User::where('role', User::ROLE_SUPERADMIN)->count() <= 1, 422, 'The last superadmin cannot be deleted.');

        if ($user->photo) {
            Storage::disk('public')->delete($user->photo);
        }
        $user->tokens()->delete();
        $user->delete();   // login_logs.user_id null (history safe)

        return response()->json(['message' => 'User deleted successfully.']);
    }
}