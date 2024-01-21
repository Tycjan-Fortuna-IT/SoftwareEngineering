<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Game extends Model
{
    use HasFactory, SoftDeletes;

    // Game stages
    public const LOBBY = 1;
    public const PLAYING = 2;
    public const FINISHED = 3;
    public const CANCELLED = 4;

    // Types of games
    public const GAME_1 = 1;
    public const GAME_2 = 2;
    public const GAME_3 = 3;
    public const GAME_4 = 4;
    public const GAME_5 = 5;
    public const GAME_6 = 6;
    public const GAME_7 = 7;

    // Game goals TODO: specify actual values for each game
    private const GAME_1_GOAL = 30;
    private const GAME_2_GOAL = 20;
    private const GAME_3_GOAL = 75;
    private const GAME_4_GOAL = 40;
    private const GAME_5_GOAL = 150;
    private const GAME_6_GOAL = 60;
    private const GAME_7_GOAL = 70;

    // Experience modifiers TODO: specify actual values for each game
    private const GAME_1_EXP_MODIFIER = 1.14;
    private const GAME_2_EXP_MODIFIER = 1.14;
    private const GAME_3_EXP_MODIFIER = 1.14;
    private const GAME_4_EXP_MODIFIER = 1.14;
    private const GAME_5_EXP_MODIFIER = 1.14;
    private const GAME_6_EXP_MODIFIER = 1.14;
    private const GAME_7_EXP_MODIFIER = 1.14;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'limit',
        'stage',
        'level',
        'goal',
        'user_id'
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
     * Get the goal for the game.
     *
     * @return int
     */
    public function getGoalAttribute() : int
    {
        return match($this->level) {
            self::GAME_1 => self::GAME_1_GOAL,
            self::GAME_2 => self::GAME_2_GOAL,
            self::GAME_3 => self::GAME_3_GOAL,
            self::GAME_4 => self::GAME_4_GOAL,
            self::GAME_5 => self::GAME_5_GOAL,
            self::GAME_6 => self::GAME_6_GOAL,
            self::GAME_7 => self::GAME_7_GOAL,
        };
    }

    /**
     * Get the experience modifier for the game.
     *
     * @return float
     */
    public function getExpModifierAttribute() : float
    {
        return match($this->level) {
            self::GAME_1 => self::GAME_1_EXP_MODIFIER,
            self::GAME_2 => self::GAME_2_EXP_MODIFIER,
            self::GAME_3 => self::GAME_3_EXP_MODIFIER,
            self::GAME_4 => self::GAME_4_EXP_MODIFIER,
            self::GAME_5 => self::GAME_5_EXP_MODIFIER,
            self::GAME_6 => self::GAME_6_EXP_MODIFIER,
            self::GAME_7 => self::GAME_7_EXP_MODIFIER,
        };
    }

    /**
     * Standard boot function for defining proper action handling.
     *
     * @return void
     */
    public static function boot() : void
    {
        parent::boot();

        static::creating(function (Game $game) {
            $game->uuid = Str::uuid();
        });
    }

    /**
     * Get the user that hosts the game.
     *
     * @return BelongsTo
     */
    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the users that are in the game.
     *
     * @return BelongsToMany
     */
    public function users() : BelongsToMany
    {
        return $this->belongsToMany(User::class)
                    ->withPivot('collected_points')
                    ->withTimestamps();
    }
}
