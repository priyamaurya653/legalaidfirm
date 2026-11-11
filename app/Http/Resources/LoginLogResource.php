<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LoginLogResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $user = $this->relationLoaded('user') ? $this->user : null;

        return [
            'id'            => $this->id,
            'role'          => $this->role,
            'role_label'    => $this->role ? (User::ROLE_LABELS[$this->role] ?? ucfirst($this->role)) : 'Unknown',
            'status'        => $this->status,
            'reason'        => $this->reason,
            'login_input'   => $this->login_input,
            'ip_address'    => $this->ip_address,
            'device'        => $this->device,
            'browser'       => $this->browser,
            'logged_in_at'  => $this->logged_in_at?->format('d M Y, h:i A'),
            'logged_in_ago' => $this->logged_in_at?->diffForHumans(),
            'logged_out_at' => $this->logged_out_at?->format('d M Y, h:i A'),
            'user'          => $user ? [
                'id'        => $user->id,
                'name'      => $user->name,
                'email'     => $user->email,
                'photo_url' => $user->photo ? asset('storage/' . $user->photo) : null,
            ] : null,
        ];
    }
}