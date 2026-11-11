<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminSignupRequest;
use App\Http\Resources\AdminResource;
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

/**
 * Superadmin only – Admins ka CRUD
 * Create / Edit form = Sign Up wale fields (users + user_profiles)
 */
class AdminController extends Controller
{
    /** GET /api/admins?search=&status=&page=&per_page= */
    public function index(Request $request): AnonymousResourceCollection
    {
        $request->validate([
            'search'   => ['nullable', 'string', 'max:100'],
            'status'   => ['nullable', Rule::in(config('advocate.statuses'))],
            'per_page' => ['nullable', 'integer', 'min:5', 'max:50'],
        ]);

        $admins = User::query()
            ->where('role', User::ROLE_ADMIN)
            ->with(['advocateProfile', 'userProfile'])
            ->withCount('assignedClients')          // is admin ke clients (list me dikhte hain)
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('search'), function ($q) use ($request) {
                $s = '%' . $request->search . '%';
                $q->where(function ($w) use ($s) {
                    $w->where('name', 'like', $s)
                      ->orWhere('email', 'like', $s)
                      ->orWhere('phone', 'like', $s)
                      ->orWhereHas('userProfile', fn ($p) => $p->where('city', 'like', $s)->orWhere('state', 'like', $s))
                      ->orWhereHas('advocateProfile', fn ($p) => $p
                          ->where('enrollment_no', 'like', $s)
                          ->orWhere('city', 'like', $s));
                });
            })
            ->latest()
            ->paginate($request->integer('per_page', 10))
            ->withQueryString();

        return AdminResource::collection($admins);
    }

    /** POST /api/admins  (multipart) */
    public function store(AdminSignupRequest $request): JsonResponse
    {
        $user = $this->saveAdmin($request, $request->validated(), null, $request->input('status') ?: 'active');

        return response()->json([
            'message' => 'Admin created successfully. They can now log in with their email and password.',
            'data'    => new AdminResource($user),
        ], 201);
    }

    /** GET /api/admins/{admin} */
    public function show(User $admin): AdminResource
    {
        $this->ensureAdmin($admin);

        return new AdminResource($admin->load(['advocateProfile', 'userProfile'])->loadCount('assignedClients'));
    }

    /** POST /api/admins/{admin}  (multipart – files ke liye POST) */
    public function update(AdminSignupRequest $request, User $admin): JsonResponse
    {
        $this->ensureAdmin($admin);

        $user = $this->saveAdmin($request, $request->validated(), $admin, $request->input('status'));

        if ($user->status !== 'active') {
            $user->tokens()->delete();   // deactivate hote hi logout
        }

        return response()->json([
            'message' => 'Admin details updated successfully.',
            'data'    => new AdminResource($user),
        ]);
    }

    /** PATCH /api/admins/{admin}/status   body: { status: active|pending|inactive } */
    public function updateStatus(Request $request, User $admin): JsonResponse
    {
        $this->ensureAdmin($admin);

        $data = $request->validate([
            'status' => ['required', Rule::in(config('advocate.statuses'))],
        ]);

        $wasPending = $admin->status === 'pending';
        $admin->update(['status' => $data['status']]);

        if ($data['status'] !== 'active') {
            $admin->tokens()->delete();
        }

        $message = match (true) {
            $data['status'] === 'active' && $wasPending => 'Admin approved successfully.',
            $data['status'] === 'active'                => 'Admin activated successfully.',
            $data['status'] === 'inactive'              => 'Admin deactivated successfully.',
            default                                     => 'Status updated.',
        };

        return response()->json([
            'message' => $message,
            'data'    => new AdminResource($admin->load('advocateProfile')),
        ]);
    }

    /** DELETE /api/admins/{admin} */
    public function destroy(User $admin): JsonResponse
    {
        $this->ensureAdmin($admin);

        $this->deleteFiles($admin);
        $admin->tokens()->delete();
        $admin->delete();   // advocate_profiles cascade delete

        return response()->json(['message' => 'Admin deleted successfully.']);
    }

    /** users + user_profiles me admin save karo (Sign Up jaise fields) */
    private function saveAdmin(Request $request, array $data, ?User $user, ?string $status): User
    {
        return DB::transaction(function () use ($request, $data, $user, $status) {
            $user ??= new User();

            $user->forceFill([
                'name'  => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'role'  => User::ROLE_ADMIN,
            ]);
            if (! empty($data['password'])) {
                $user->password = Hash::make($data['password']);
            }
            if ($status) {
                $user->status = $status;
            }
            if ($request->hasFile('photo')) {
                if ($user->photo && Storage::disk('public')->exists($user->photo)) {
                    Storage::disk('public')->delete($user->photo);
                }
                $user->photo = $request->file('photo')->store('users/photos', 'public');
            }
            $user->save();

            $user->userProfile()->updateOrCreate(['user_id' => $user->id], Arr::only($data, UserProfile::FIELDS));

            return $user->load(['advocateProfile', 'userProfile']);
        });
    }

    /** photo + (purane advocate profile ke) id_proof / certificate files delete */
    private function deleteFiles(User $user): void
    {
        $paths = [$user->photo];
        if ($p = $user->advocateProfile) {
            $paths[] = $p->id_proof ?? null;
            $paths[] = $p->enrollment_certificate ?? null;
        }
        foreach (array_filter($paths) as $path) {
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }
    }

    private function ensureAdmin(User $user): void
    {
        abort_unless($user->role === User::ROLE_ADMIN, 404, 'Admin not found.');
    }
}