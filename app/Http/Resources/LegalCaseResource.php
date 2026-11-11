<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LegalCaseResource extends JsonResource
{
    /** true = poora Aadhaar / PAN (sirf superadmin ke show/edit me) */
    public bool $full = false;

    public function withFull(bool $full = true): static
    {
        $this->full = $full;

        return $this;
    }

    public function toArray(Request $request): array
    {
        $api  = url('/api/cases/' . $this->id);
        $full = $this->full && $request->user()?->role === User::ROLE_SUPERADMIN;

        return [
            'id'                => $this->id,
            'case_no'           => $this->case_no,
            'title'             => $this->title,
            'status'            => $this->status,
            'status_label'      => $this->statusLabel(),
            'priority'          => $this->priority,
            'next_hearing_date' => $this->next_hearing_date?->format('Y-m-d'),
            'next_hearing'      => $this->next_hearing_date?->format('d M Y'),

            'practice_area_id'  => $this->practice_area_id,
            'practice_area'     => $this->whenLoaded('practiceArea', fn () => $this->practiceArea ? [
                'id' => $this->practiceArea->id, 'name' => $this->practiceArea->name, 'type' => $this->practiceArea->type,
            ] : null),
            'advocate_id'       => $this->advocate_id,
            'advocate'          => $this->whenLoaded('advocate', fn () => $this->advocate ? [
                'id' => $this->advocate->id, 'name' => $this->advocate->name, 'email' => $this->advocate->email, 'phone' => $this->advocate->phone,
            ] : null),

            'client_name'       => $this->client_name,
            'client_phone'      => $this->client_phone,
            'client_email'      => $this->client_email,

            // KYC – listing me masked, edit me full
            'aadhaar_masked'    => $this->maskedAadhaar(),
            'aadhaar_number'    => $this->when($full, fn () => $this->aadhaar_number),
            'aadhaar_file_url'  => $this->aadhaar_file ? $api . '/files/aadhaar' : null,
            'pan_masked'        => $this->maskedPan(),
            'pan_number'        => $this->when($full, fn () => $this->pan_number),
            'pan_file_url'      => $this->pan_file ? $api . '/files/pan' : null,

            'court_name'        => $this->court_name,
            'opponent_name'     => $this->opponent_name,
            'filing_date'       => $this->filing_date?->format('Y-m-d'),
            'description'       => $this->description,

            'documents_count'   => $this->whenCounted('documents'),
            'documents'         => $this->whenLoaded('documents', fn () => $this->documents->map(fn ($d) => [
                'id'            => $d->id,
                'title'         => $d->title,
                'original_name' => $d->original_name,
                'mime'          => $d->mime,
                'size'          => $d->humanSize(),
                'url'           => $api . '/documents/' . $d->id,
                'uploaded_at'   => $d->created_at?->format('d M Y'),
            ])->values()),

            'created_by'        => $this->whenLoaded('creator', fn () => $this->creator?->name),
            'updated_by'        => $this->whenLoaded('editor', fn () => $this->editor?->name),
            'created_at'        => $this->created_at?->format('d M Y'),
            'updated_at'        => $this->updated_at?->format('d M Y, h:i A'),
        ];
    }
}