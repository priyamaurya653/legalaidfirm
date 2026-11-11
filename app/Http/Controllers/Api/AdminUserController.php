<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AdminResource;
use App\Models\PracticeArea;
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
 * Admin (Advocate) apne users (clients) banata hai
 *  - user ka role = "user", created_by = admin ki id
 *  - user apni email + password se login karke /dashboard par jaata hai
 *  - admin sirf apne banaye users dekh / badal / delete kar sakta hai
 *    (superadmin ko sab users dikhte hain)
 * Header: Authorization: Bearer {token}
 */
class AdminUserController extends Controller
{
    /** GET /api/my-users/options – form dropdowns */
    public function options(): JsonResponse
    {
        return response()->json([
            'practice_areas' => PracticeArea::active()->ordered()->pluck('name'),
            'states'         => config('advocate.states', []),
            'genders'        => config('advocate.genders', ['Male', 'Female', 'Other']),
        ]);
    }

    /** GET /api/my-users?search=&status=&page= */
    public function index(Request $request): AnonymousResourceCollection
    {
        $request->validate([
            'search'   => ['nullable', 'string', 'max:100'],
            'status'   => ['nullable', Rule::in(['active', 'inactive'])],
            'per_page' => ['nullable', 'integer', 'min:5', 'max:100'],
        ]);

        $base = $this->scope($request);

        $users = (clone $base)
            ->with(['userProfile', 'creator:id,name,email,phone'])
            ->withCount('loginLogs')
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('search'), function ($q) use ($request) {
                $s = '%' . $request->search . '%';
                $q->where(fn ($w) => $w->where('name', 'like', $s)->orWhere('email', 'like', $s)->orWhere('phone', 'like', $s)
                    ->orWhereHas('userProfile', fn ($p) => $p->where('city', 'like', $s)->orWhere('interested_in', 'like', $s)));
            })
            ->latest()
            ->paginate($request->integer('per_page', 10))
            ->withQueryString();

        return AdminResource::collection($users)->additional([
            'stats' => [
                'total'    => (clone $base)->count(),
                'active'   => (clone $base)->where('status', 'active')->count(),
                'inactive' => (clone $base)->where('status', '!=', 'active')->count(),
                'new_week' => (clone $base)->where('created_at', '>=', now()->subDays(7))->count(),
            ],
        ]);
    }

    /** POST /api/my-users  (multipart) */
    public function store(Request $request): JsonResponse
    {
        // subscription plan ki user limit
        if ($request->user()->role === User::ROLE_ADMIN) {
            $sub = \App\Models\AdminSubscription::currentFor($request->user()->id);
            if ($sub && $sub->max_users && User::where('created_by', $request->user()->id)->count() >= $sub->max_users) {
                return response()->json([
                    'message' => 'Your "' . $sub->plan_name . '" plan allows only ' . $sub->max_users . ' users. Contact the superadmin to upgrade.',
                    'code'    => 'user_limit_reached',
                ], 403);
            }
        }

        $data = $this->validated($request);
        $user = $this->save($request, $data, new User());

        return response()->json([
            'message' => 'User "' . $user->name . '" created. They can now log in with ' . $user->email . ' and their password.',
            'data'    => new AdminResource($user),
        ], 201);
    }

    /** GET /api/my-users/{user} */
    public function show(Request $request, User $user): JsonResponse
    {
        $this->authorizeUser($request, $user);
        $user->load(['userProfile', 'creator:id,name,email,phone'])->loadCount('loginLogs');

        return response()->json([
            'data'   => new AdminResource($user),
            'logins' => $user->loginLogs()->latest('logged_in_at')->limit(5)->get()->map(fn ($l) => [
                'status' => $l->status,
                'device' => trim(($l->browser ?? '') . ' / ' . ($l->device ?? ''), ' /'),
                'ip'     => $l->ip_address,
                'ago'    => $l->logged_in_at?->diffForHumans(),
            ]),
        ]);
    }

    /** POST /api/my-users/{user}  (multipart) */
    public function update(Request $request, User $user): JsonResponse
    {
        $this->authorizeUser($request, $user);
        $data = $this->validated($request, $user);
        $user = $this->save($request, $data, $user);

        if ($user->status !== 'active' || ! empty($data['password'])) {
            $user->tokens()->delete();   // deactivate / password change -> logout
        }

        return response()->json([
            'message' => 'User "' . $user->name . '" updated successfully.',
            'data'    => new AdminResource($user),
        ]);
    }

    /** PATCH /api/my-users/{user}/status  { status: active|inactive } */
    public function updateStatus(Request $request, User $user): JsonResponse
    {
        $this->authorizeUser($request, $user);
        $data = $request->validate(['status' => ['required', Rule::in(['active', 'inactive'])]]);
        $user->update(['status' => $data['status']]);

        if ($data['status'] !== 'active') {
            $user->tokens()->delete();
        }

        return response()->json(['message' => $data['status'] === 'active' ? 'User activated.' : 'User deactivated and logged out.']);
    }

    /** DELETE /api/my-users/{user} */
    public function destroy(Request $request, User $user): JsonResponse
    {
        $this->authorizeUser($request, $user);

        if ($user->photo) {
            Storage::disk('public')->delete($user->photo);
        }
        $user->tokens()->delete();
        $user->delete();

        return response()->json(['message' => 'User deleted successfully.']);
    }

    /* ---------------------------------------------------------- */

    /** admin = sirf apne banaye users, superadmin = sab "user" role wale */
    private function scope(Request $request)
    {
        $me = $request->user();

        return User::query()->where('role', User::ROLE_USER)
            ->when($me->role !== User::ROLE_SUPERADMIN, fn ($q) => $q->where('created_by', $me->id));
    }

    private function authorizeUser(Request $request, User $user): void
    {
        $me = $request->user();
        abort_unless(
            $user->role === User::ROLE_USER && ($me->role === User::ROLE_SUPERADMIN || (int) $user->created_by === (int) $me->id),
            404,
            'User not found.'
        );
    }

    private function validated(Request $request, ?User $user = null): array
    {
        $request->merge([
            'email'     => strtolower(trim((string) $request->email)),
            'phone'     => preg_replace('/\D/', '', (string) $request->phone),
            'alt_phone' => preg_replace('/\D/', '', (string) $request->alt_phone) ?: null,
        ]);

        return $request->validate([
            'name'          => ['required', 'string', 'min:3', 'max:100'],
            'email'         => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user?->id)],
            'phone'         => ['required', 'regex:/^[6-9]\d{9}$/', Rule::unique('users', 'phone')->ignore($user?->id)],
            'password'      => [$user ? 'nullable' : 'required', 'confirmed', Password::min(8)->letters()->mixedCase()->numbers()],
            'photo'         => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'gender'        => ['required', Rule::in(config('advocate.genders', ['Male', 'Female', 'Other']))],
            'dob'           => ['nullable', 'date', 'before:today'],
            'occupation'    => ['nullable', 'string', 'max:100'],
            'alt_phone'     => ['nullable', 'regex:/^[6-9]\d{9}$/', 'different:phone'],
            'address'       => ['required', 'string', 'max:500'],
            'city'          => ['required', 'string', 'max:80'],
            'state'         => ['required', 'string', 'max:60'],
            'pincode'       => ['required', 'digits:6'],
            'interested_in' => ['required', 'string', 'max:120'],      // case kis practice area ka hai
            'heard_from'    => ['nullable', 'string', 'max:255'],      // client kaise aaya (referral / walk-in …)
            'status'        => ['nullable', Rule::in(['active', 'inactive'])],
        ], [
            'email.unique'           => 'This email is already registered.',
            'phone.unique'           => 'This mobile number is already registered.',
            'phone.regex'            => 'Enter a valid 10-digit mobile number.',
            'alt_phone.regex'        => 'Enter a valid 10-digit alternate number.',
            'alt_phone.different'    => 'Alternate number must be different.',
            'password.required'      => 'Please set a login password for the user.',
            'password.confirmed'     => 'Passwords do not match.',
            'interested_in.required' => 'Please select the case / practice area.',
            'pincode.digits'         => 'Pincode must be 6 digits.',
        ]);
    }

    private function save(Request $request, array $data, User $user): User
    {
        DB::transaction(function () use ($request, $data, $user) {
            $isNew = ! $user->exists;

            $user->forceFill([
                'name'   => $data['name'],
                'email'  => $data['email'],
                'phone'  => $data['phone'],
                'role'   => User::ROLE_USER,
                'status' => $data['status'] ?? ($user->status ?: 'active'),
            ]);
            if ($isNew) {
                // superadmin bhi bana sakta hai – tab created_by = null nahi, superadmin ka id na ho isliye sirf admin ka id
                $user->created_by = $request->user()->role === User::ROLE_ADMIN ? $request->user()->id : ($request->input('created_by') ?: null);
            }
            if (! empty($data['password'])) {
                $user->password = Hash::make($data['password']);
            }
            if ($request->hasFile('photo')) {
                if ($user->photo) Storage::disk('public')->delete($user->photo);
                $user->photo = $request->file('photo')->store('users/photos', 'public');
            }
            $user->save();

            $user->userProfile()->updateOrCreate(['user_id' => $user->id], Arr::only($data, UserProfile::FIELDS));
        });

        return $user->fresh()->load(['userProfile', 'creator:id,name,email,phone']);
    }
}