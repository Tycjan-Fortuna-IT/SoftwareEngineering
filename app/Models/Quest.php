<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Quest extends Model
{
    use HasFactory, SoftDeletes;

    // Types of quests
    public const GAME_1 = 1;
    public const GAME_2 = 2;
    public const GAME_3 = 3;
    public const GAME_4 = 4;
    public const GAME_5 = 5;
    public const GAME_6 = 6;
    public const GAME_7 = 7;

    // Statuses
    public const STATUS_IN_PROGRESS = 8;
    public const STATUS_COMPLETED = 9;
    public const STATUS_FAILED = 10;
    public const STATUS_EXPIRED = 11;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'type',
        'status',
        'required',
        'collected',
        'reward',
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
     * Get the name of the quest.
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

        return 'Unknown Quest Type - ' . $this->type;
    }

    /**
     * Get the status of the quest.
     *
     * @return string
     */
    public function getStatusNameAttribute() : string
    {
        $reflection = new \ReflectionClass($this);
        $constants = $reflection->getConstants();

        foreach ($constants as $key => $value) {
            if ($value === $this->status) {
                return $key;
            }
        }

        return 'Unknown Quest Status - ' . $this->status;
    }

    /**
     * Standard boot function for defining proper action handling.
     *
     * @return void
     */
    public static function boot() : void
    {
        parent::boot();

        static::creating(function (Quest $quest) {
            $quest->uuid = Str::uuid();
        });
    }

    /**
     * Get the user that owns the quest.
     *
     * @return BelongsTo
     */
    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include quests of a given user.
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
