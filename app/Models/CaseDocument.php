<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CaseDocument extends Model
{
    protected $fillable = ['legal_case_id', 'title', 'path', 'original_name', 'mime', 'size', 'uploaded_by'];

    protected function casts(): array
    {
        return ['size' => 'integer'];
    }

    public function legalCase(): BelongsTo { return $this->belongsTo(LegalCase::class); }
    public function uploader(): BelongsTo  { return $this->belongsTo(User::class, 'uploaded_by'); }

    public function humanSize(): string
    {
        $b = $this->size;

        return $b >= 1048576 ? round($b / 1048576, 2) . ' MB' : max(1, round($b / 1024)) . ' KB';
    }
}