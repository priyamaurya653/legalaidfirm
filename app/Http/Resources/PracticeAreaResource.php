<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PracticeAreaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'name'              => $this->name,
            'type'              => $this->type,
            'type_label'        => $this->type,
            'slug'              => $this->slug,
            'icon'              => $this->icon,
            'image'             => $this->image,
            'image_url'         => $this->imageUrl(),
            'short_description' => $this->short_description,
            'description'       => $this->description,
            'sort_order'        => $this->sort_order,
            'status'            => $this->status,
            // website par abhi sirf config/practice.php wale pages bane hain
            'url'               => array_key_exists($this->slug, config('practice', [])) ? url('/practice-areas/' . $this->slug) : null,
            'created_by'        => $this->whenLoaded('creator', fn () => $this->creator?->name),
            'updated_by'        => $this->whenLoaded('editor', fn () => $this->editor?->name),
            'created_at'        => $this->created_at?->format('d M Y'),
            'updated_at'        => $this->updated_at?->format('d M Y, h:i A'),
        ];
    }
}