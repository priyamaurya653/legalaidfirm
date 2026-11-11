<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AdminResource;
use App\Models\LoginLog;
use App\Models\UserProfile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

/**
 * My Profile – logged-in user (superadmin / admin / user) apni profile dekhe aur badle
 * Header: Authorization: Bearer {token}
 */
class ProfileController extends Controller
{
    /** GET /api/profile */
    public function show(Request $request): JsonResponse
    {
        $user = $request->user()->load('userProfile')->loadCount('loginLogs');

        return response()->json([
            'data'          => new AdminResource($user),
            'recent_logins' => LoginLog::where('user_id', $user->id)->latest('logged_in_at')->limit(5)->get()
                ->map(fn ($l) => [
                    'status'  => $l->status,
                    'ip'      => $l->ip_address,
                    'device'  => trim(($l->browser ?? '') . ' · ' . ($l->device ?? ''), ' ·'),
                    'at'      => $l->logged_in_at?->format('d M Y, h:i A'),
                    'ago'     => $l->logged_in_at?->diffForHumans(),
                ]),
            'states'        => config('advocate.states', []),
            'genders'       => config('advocate.genders', ['Male', 'Female', 'Other']),
        ]);
    }

    /** POST /api/profile  (multipart – photo ke liye) */
    public function update(Request $request): JsonResponse
    {
        $user = $request->user();

        $request->merge([
            'email' => strtolower(trim((string) $request->email)),
            'phone' => preg_replace('/\D/', '', (string) $request->phone) ?: null,
        ]);

        $data = $request->validate([
            'name'         => ['required', 'string', 'min:3', 'max:100'],
            'email'        => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user->id)],
            'phone'        => ['nullable', 'digits:10', Rule::unique('users', 'phone')->ignore($user->id)],
            'photo'        => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_photo' => ['nullable', 'boolean'],
            'gender'       => ['nullable', 'string', 'max:20'],
            'dob'          => ['nullable', 'date', 'before:today'],
            'occupation'   => ['nullable', 'string', 'max:100'],
            'address'      => ['nullable', 'string', 'max:500'],
            'city'         => ['nullable', 'string', 'max:80'],
            'state'        => ['nullable', 'string', 'max:60'],
            'pincode'      => ['nullable', 'digits:6'],
        ], [
            'email.unique' => 'This email is already used by another account.',
            'phone.unique' => 'This phone number is already used by another account.',
            'phone.digits' => 'Phone must be a 10-digit number.',
            'photo.max'    => 'Photo must be smaller than 2MB.',
        ]);

        DB::transaction(function () use ($request, $data, $user) {
            $user->forceFill(['name' => $data['name'], 'email' => $data['email'], 'phone' => $data['phone'] ?? null]);

            if ($request->boolean('remove_photo') && $user->photo) {
                Storage::disk('public')->delete($user->photo);
                $user->photo = null;
            }
            if ($request->hasFile('photo')) {
                if ($user->photo) Storage::disk('public')->delete($user->photo);
                $user->photo = $request->file('photo')->store('users/photos', 'public');
            }
            $user->save();

            $profile = Arr::only($data, ['gender', 'dob', 'occupation', 'address', 'city', 'state', 'pincode']);
            $user->userProfile()->updateOrCreate(['user_id' => $user->id], Arr::only($profile, UserProfile::FIELDS));
        });

        return response()->json([
            'message' => 'Profile updated successfully.',
            'data'    => new AdminResource($user->fresh()->load('userProfile')),
        ]);
    }

    /** POST /api/profile/password  body: current_password, password, password_confirmation, logout_others */
    public function password(Request $request): JsonResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'current_password' => ['required', 'string'],
            'password'         => ['required', 'confirmed', 'different:current_password', Password::min(8)->letters()->mixedCase()->numbers()],
            'logout_others'    => ['nullable', 'boolean'],
        ], [
            'password.different' => 'New password must be different from the current password.',
            'password.confirmed' => 'Passwords do not match.',
        ]);

        if (! Hash::check($data['current_password'], $user->password)) {
            return response()->json([
                'message' => 'Current password is incorrect.',
                'errors'  => ['current_password' => ['Current password is incorrect.']],
            ], 422);
        }

        $user->forceFill(['password' => Hash::make($data['password'])])->save();

        // baaki devices se logout (current token rehta hai)
        if ($request->boolean('logout_others', true)) {
            $current = $user->currentAccessToken();
            $user->tokens()->when($current && isset($current->id), fn ($q) => $q->where('id', '!=', $current->id))->delete();
        }

        return response()->json(['message' => 'Password changed successfully.']);
    }
}