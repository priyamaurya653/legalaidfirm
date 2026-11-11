<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EnquiryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'email'         => $this->email,
            'phone'         => $this->phone,
            'practice_area' => $this->practiceArea?->name ?? $this->practice_area ?? 'General',
            'message'       => $this->message,
            'status'        => $this->status,
            'status_label'  => $this->statusLabel(),
            'is_read'       => (bool) $this->read_at,
            'time_ago'      => $this->created_at?->diffForHumans(),
            'created_at'    => $this->created_at?->format('d M Y, h:i A'),
            'read_at'       => $this->read_at?->format('d M Y, h:i A'),
            'ip'            => $this->ip,
        ];
    }
}