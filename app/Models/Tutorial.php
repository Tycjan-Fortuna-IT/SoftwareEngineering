<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tutorial extends Model
{
    use HasFactory;

    // Types of tutorials
    public const INTRODUCTION = 1;
    public const GAME_1 = 2;
    public const GAME_2 = 3;
    public const GAME_3 = 4;
    public const GAME_4 = 5;
    public const GAME_5 = 6;
    public const GAME_6 = 7;
    public const GAME_7 = 8;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'type',
        'completed',
        'user_id',
    ];

    /**
     * Get the route key.
     *
     * @return string
     */
    public function getRouteKeyName() : string
    {
        return 'uuid';
    }

    /**
     * Get the name of the tutorial.
     *
     * @return string
     */
    public function getNameAttribute() : string
    {
        $reflection = new \ReflectionClass($this);
        $constants = $reflection->getConstants();

        foreach ($constants as $key => $value) {
            if ($value === $this->type) {
                return $key;
            }
        }

        return 'Unknown Tutorial Type - ' . $this->type;
    }

    /**
     * Standard boot function for defining proper action handling.
     *
     * @return void
     */
    public static function boot() : void
    {
        parent::boot();

        static::creating(function (Tutorial $tutorial) {
            $tutorial->uuid = Str::uuid();
        });
    }

    /**
     * Get the user that owns the comment.
     *
     * @return BelongsTo
     */
    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include tutorials of a given user.
     *
     * @param Builder $query
     * @param string $uuid
     * @return Builder
     */
    public function scopeUserUuid(Builder $query, string $uuid) : Builder
    {
        return $query->whereHas('user', function (Builder $query) use ($uuid) {
            $query->where('uuid', $uuid);
        });
    }
}
