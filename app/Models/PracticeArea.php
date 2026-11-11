<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PracticeArea extends Model
{
    public const STATUSES = ['active', 'inactive'];

    protected $fillable = [
        'name', 'type', 'slug', 'icon', 'image', 'short_description',
        'description', 'sort_order', 'status', 'created_by', 'updated_by',
    ];

    protected function casts(): array
    {
        return ['sort_order' => 'integer'];
    }

    /* ---------- relations ---------- */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /* ---------- scopes ---------- */
    public function scopeActive(Builder $q): Builder
    {
        return $q->where('status', 'active');
    }

    public function scopeOrdered(Builder $q): Builder
    {
        return $q->orderBy('sort_order')->orderBy('name');
    }

    /* ---------- helpers ---------- */
    /** DB me jitne alag types save hain (dynamic) */
    public static function typeList(): array
    {
        return static::query()->whereNotNull('type')->where('type', '!=', '')
            ->distinct()->orderBy('type')->pluck('type')->all();
    }

    public function imageUrl(): ?string
    {
        if (! $this->image) {
            return null;
        }

        return Str::startsWith($this->image, ['http://', 'https://'])
            ? $this->image
            : Storage::disk('public')->url($this->image);
    }

    /** name se unique slug banao (edit me apna id ignore) */
    public static function uniqueSlug(string $text, ?int $ignoreId = null): string
    {
        $base = Str::slug($text) ?: 'practice-area';
        $slug = $base;
        $i    = 2;

        while (static::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }
}