<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JobOpeningResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                 => $this->id,
            'title'              => $this->title,
            'job_type'           => $this->job_type,
            'experience'         => $this->experience,
            'location'           => $this->location,
            'openings'           => $this->openings,
            'salary'             => $this->salary,
            'description'        => $this->description,
            'requirements'       => $this->requirements,
            'last_date'          => $this->last_date?->format('Y-m-d'),
            'last_date_label'    => $this->last_date?->format('d M Y'),
            'sort_order'         => $this->sort_order,
            'status'             => $this->status,
            'is_live'            => $this->isLive(),
            'applications_count' => $this->whenCounted('applications'),
            'new_applications'   => $this->when(isset($this->new_applications_count), fn () => (int) $this->new_applications_count),
            'created_at'         => $this->created_at?->format('d M Y'),
            'updated_at'         => $this->updated_at?->format('d M Y, h:i A'),
        ];
    }
}