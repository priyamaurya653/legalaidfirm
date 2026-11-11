<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
{
    public bool $full = false;

    public function withFull(bool $full = true): static
    {
        $this->full = $full;

        return $this;
    }

    public function toArray(Request $request): array
    {
        $api  = url('/api/clients/' . $this->id);
        // poora Aadhaar/PAN: superadmin + assigned admin (controller pehle hi access check karta hai)
        $full = $this->full && in_array($request->user()?->role, [User::ROLE_SUPERADMIN, User::ROLE_ADMIN], true);

        return [
            'id'      => $this->id,
            // login account (client = user)
            'user_id'       => $this->user_id,
            'has_login'     => (bool) $this->user_id,
            'login_status'  => $this->whenLoaded('user', fn () => $this->user?->status),
            'last_login_at' => $this->whenLoaded('user', fn () => $this->user?->last_login_at?->diffForHumans()),
            'name'    => $this->name,
            'phone'   => $this->phone,
            'email'   => $this->email,
            'city'    => $this->city,
            'address' => $this->address,

            'practice_area_id' => $this->practice_area_id,
            'practice_area'    => $this->whenLoaded('practiceArea', fn () => $this->practiceArea?->name),
            'case_details'     => $this->case_details,
            'advocate_id'      => $this->advocate_id,
            'advocate'         => $this->whenLoaded('advocate', fn () => $this->advocate ? [
                'id' => $this->advocate->id, 'name' => $this->advocate->name, 'phone' => $this->advocate->phone, 'email' => $this->advocate->email,
            ] : null),

            'aadhaar_masked'   => $this->maskedAadhaar(),
            'aadhaar_number'   => $this->when($full, fn () => $this->aadhaar_number),
            'aadhaar_file_url' => $this->aadhaar_file ? $api . '/files/aadhaar' : null,
            'pan_masked'       => $this->maskedPan(),
            'pan_number'       => $this->when($full, fn () => $this->pan_number),
            'pan_file_url'     => $this->pan_file ? $api . '/files/pan' : null,

            'alt_phone' => $this->alt_phone,
            'state'     => $this->state,
            'pincode'   => $this->pincode,
            'status'    => $this->status,

            'cases_count'     => (int) ($this->cases_count ?? 0),
            'documents_count' => $this->whenCounted('documents'),
            'documents'       => $this->whenLoaded('documents', fn () => $this->documents->map(fn ($d) => [
                'id'            => $d->id,
                'title'         => $d->title,
                'original_name' => $d->original_name,
                'size'          => $d->humanSize(),
                'url'           => $api . '/documents/' . $d->id,
                'uploaded_at'   => $d->created_at?->format('d M Y'),
            ])->values()),

            'created_by'    => $this->whenLoaded('creator', fn () => $this->creator?->name),
            'created_by_id' => $this->created_by,
            'updated_by' => $this->whenLoaded('editor', fn () => $this->editor?->name),
            'since'      => $this->created_at?->format('M Y'),
            'created_at' => $this->created_at?->format('d M Y'),
            'updated_at' => $this->updated_at?->format('d M Y, h:i A'),
        ];
    }
}