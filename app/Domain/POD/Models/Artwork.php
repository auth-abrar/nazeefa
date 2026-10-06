<?php

namespace App\Domain\POD\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Artwork extends Model
{
    protected $fillable = [
        'user_id',
        'file_path',
        'thumbnail_path',
        'original_filename',
        'mime_type',
        'file_size_bytes',
        'print_placement',
        'canvas_metadata',
    ];

    protected $casts = [
        'canvas_metadata' => 'array',
        'file_size_bytes' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function proofs(): HasMany
    {
        return $this->hasMany(DesignProof::class);
    }
}
