<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminSubscriptionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $total = max(1, (int) $this->duration_days);
        $left  = $this->daysLeft();

        return [
            'id'            => $this->id,
            'plan_id'       => $this->plan_id,
            'plan_name'     => $this->plan_name,
            'price'         => (float) $this->price,
            'duration_days' => $this->duration_days,
            'max_users'     => $this->max_users,
            'features'      => $this->whenLoaded('plan', fn () => $this->plan?->features ?? []),
            'status'        => $this->status,
            'status_label'  => $this->statusLabel(),
            'note'          => $this->note,
            'days_left'     => $left,
            'progress'      => $this->status === 'active' ? min(100, round(($total - $left) / $total * 100)) : null,
            'is_current'    => $this->status === 'active' && $this->starts_at?->isPast() && $this->ends_at?->isFuture(),
            'assigned_at'   => $this->assigned_at?->format('d M Y, h:i A'),
            'accepted_at'   => $this->accepted_at?->format('d M Y, h:i A'),
            'starts_at'     => $this->starts_at?->format('d M Y'),
            'ends_at'       => $this->ends_at?->format('d M Y'),
            'ends_at_full'  => $this->ends_at?->format('d M Y, h:i A'),
            'admin'         => $this->whenLoaded('admin', fn () => $this->admin ? [
                'id' => $this->admin->id, 'name' => $this->admin->name, 'email' => $this->admin->email, 'phone' => $this->admin->phone,
            ] : null),
            'assigned_by'   => $this->whenLoaded('assigner', fn () => $this->assigner?->name),
        ];
    }
}