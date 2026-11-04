<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Deck extends Model
{
    protected $fillable = ['name', 'description'];

    public function cards(): HasMany
    {
        return $this->hasMany(Card::class);
    }

    public function practices(): HasMany
    {
        return $this->hasMany(Practice::class);
    }
}
