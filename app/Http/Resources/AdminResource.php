<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $p = $this->relationLoaded('advocateProfile') ? $this->advocateProfile : null;
        $file = fn (?string $path) => $path ? asset('storage/' . $path) : null;

        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'email'         => $this->email,
            'phone'         => $this->phone,
            'role'          => $this->role,
            'role_label'    => $this->resource->roleLabel(),
            'logins_count'  => $this->whenCounted('loginLogs'),
            'status'        => $this->status,
            'photo_url'     => $file($this->photo),
            'last_login_at' => $this->last_login_at?->diffForHumans(),
            'created_at'    => $this->created_at?->format('d M Y'),
            'created_by_id' => $this->created_by,
            // kis admin (advocate) ne banaya
            'creator'       => $this->whenLoaded('creator', fn () => $this->creator ? [
                'id'    => $this->creator->id,
                'name'  => $this->creator->name,
                'email' => $this->creator->email,
                'phone' => $this->creator->phone,
            ] : null),
            'users_count'   => $this->whenCounted('createdUsers'),
            'clients_count' => $this->whenCounted('assignedClients'),

            'user_profile' => ($this->relationLoaded('userProfile') && $this->userProfile) ? [
                'gender'        => $this->userProfile->gender,
                'dob'           => $this->userProfile->dob?->format('Y-m-d'),
                'alt_phone'     => $this->userProfile->alt_phone,
                'occupation'    => $this->userProfile->occupation,
                'address'       => $this->userProfile->address,
                'city'          => $this->userProfile->city,
                'state'         => $this->userProfile->state,
                'pincode'       => $this->userProfile->pincode,
                'interested_in' => $this->userProfile->interested_in,
                'heard_from'    => $this->userProfile->heard_from,
            ] : null,

            'profile' => $p ? [
                'father_name'      => $p->father_name,
                'dob'              => $p->dob?->format('Y-m-d'),
                'gender'           => $p->gender,
                'alt_phone'        => $p->alt_phone,
                'enrollment_no'    => $p->enrollment_no,
                'bar_council'      => $p->bar_council,
                'enrollment_date'  => $p->enrollment_date?->format('Y-m-d'),
                'experience_years' => $p->experience_years,
                'qualification'    => $p->qualification,
                'university'       => $p->university,
                'passing_year'     => $p->passing_year,
                'specializations'  => $p->specializations ?? [],
                'courts'           => $p->courts,
                'languages'        => $p->languages,
                'bio'              => $p->bio,
                'office_address'   => $p->office_address,
                'city'             => $p->city,
                'state'            => $p->state,
                'pincode'          => $p->pincode,
                'id_proof_type'    => $p->id_proof_type,
                'id_proof_number'  => $p->id_proof_number,
                'id_proof_url'               => $file($p->id_proof),
                'enrollment_certificate_url' => $file($p->enrollment_certificate),
            ] : null,
        ];
    }
}