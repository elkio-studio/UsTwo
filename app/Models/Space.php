<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Space extends Model
{
    protected $fillable = ['name', 'owner_id'];

    protected $casts = ['movie_slot' => 'integer', 'series_slot' => 'integer'];

    protected static function booted(): void
    {
        static::creating(fn (Space $space) => $space->slug = self::availableSlug($space->name));
    }

    public static function availableSlug(string $name, ?int $exceptId = null): string
    {
        $base = Str::slug($name) ?: 'estanteria';
        $reserved = ['acceso', 'registro', 'salir', 'creditos', 'peliculas', 'espacios', 'invitaciones', 'up', 'storage'];
        $slug = $base;

        $others = self::query()->when($exceptId, fn ($query) => $query->whereKeyNot($exceptId));
        for ($suffix = 2; in_array($slug, $reserved, true) || (clone $others)->where('slug', $slug)->exists(); $suffix++) {
            $slug = $base.'-'.$suffix;
        }

        return $slug;
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function movies(): HasMany
    {
        return $this->hasMany(Movie::class);
    }

    public function series(): HasMany
    {
        return $this->hasMany(Series::class);
    }

    public function invitations(): HasMany
    {
        return $this->hasMany(SpaceInvitation::class);
    }
}
