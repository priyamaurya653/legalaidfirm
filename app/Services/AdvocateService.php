<?php

namespace App\Services;

use App\Models\AdvocateProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Advocate (Admin) ko create / update karne ka shared logic
 * – register API aur superadmin dono yahi use karte hain.
 */
class AdvocateService
{
    public function save(Request $request, array $data, ?User $user = null, ?string $status = null): User
    {
        return DB::transaction(function () use ($request, $data, $user, $status) {
            $user ??= new User();

            $user->fill(Arr::only($data, ['name', 'email', 'phone']));
            $user->role = User::ROLE_ADMIN;

            if (! empty($data['password'])) {
                $user->password = $data['password'];      // 'hashed' cast hash kar dega
            }
            if ($status) {
                $user->status = $status;
            }
            if ($request->hasFile('photo')) {
                $this->deleteFile($user->photo);
                $user->photo = $request->file('photo')->store('advocates/photos', 'public');
            }
            $user->save();

            $profile = $user->advocateProfile ?? new AdvocateProfile();
            $profile->fill(Arr::only($data, AdvocateProfile::FIELDS));

            foreach (AdvocateProfile::FILES as $field => $folder) {
                if ($request->hasFile($field)) {
                    $this->deleteFile($profile->{$field});
                    $profile->{$field} = $request->file($field)->store($folder, 'public');
                }
            }

            $user->advocateProfile()->save($profile);

            return $user->load('advocateProfile');
        });
    }

    public function deleteAllFiles(User $user): void
    {
        $this->deleteFile($user->photo);

        if ($profile = $user->advocateProfile) {
            foreach (array_keys(AdvocateProfile::FILES) as $field) {
                $this->deleteFile($profile->{$field});
            }
        }
    }

    private function deleteFile(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}