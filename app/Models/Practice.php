<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Practice extends Model
{
    protected $fillable = ['cards', 'answers', 'finished_at'];

    protected function casts(): array
    {
        return ['cards' => 'array', 'answers' => 'array', 'finished_at' => 'datetime'];
    }

    public function deck(): BelongsTo
    {
        return $this->belongsTo(Deck::class);
    }
}
