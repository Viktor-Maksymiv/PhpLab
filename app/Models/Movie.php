<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Movie extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'duration_minutes', 'age_rating', 'release_date', 'poster_url'];

    protected $casts = [
        'duration_minutes' => 'integer',
        'release_date' => 'date:Y-m-d',
    ];

    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class)->withTimestamps();
    }

    public function showtimes(): HasMany
    {
        return $this->hasMany(Showtime::class);
    }
}
