<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CareerApplicationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'job_opening_id' => $this->job_opening_id,
            'position'       => $this->job?->title ?? $this->position ?? 'General Application',
            'job_type'       => $this->job?->job_type,
            'name'           => $this->name,
            'email'          => $this->email,
            'phone'          => $this->phone,
            'cover'          => $this->cover,
            'notes'          => $this->notes,
            'status'         => $this->status,
            'status_label'   => $this->statusLabel(),
            'is_read'        => (bool) $this->read_at,
            'resume_name'    => $this->resume_name,
            'resume_url'     => $this->resume ? url('/api/applications/' . $this->id . '/resume') : null,
            'time_ago'       => $this->created_at?->diffForHumans(),
            'created_at'     => $this->created_at?->format('d M Y, h:i A'),
            'read_at'        => $this->read_at?->format('d M Y, h:i A'),
        ];
    }
}