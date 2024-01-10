<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;
use \Illuminate\Database\Eloquent\Relations\BelongsToMany;
use \Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'avatar',
        'about',
        'email',
        'password',
        'level',
        'experience',
        'anonymous',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
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
     * Standard boot function for defining proper action handling.
     *
     * @return void
     */
    public static function boot() : void
    {
        parent::boot();

        static::creating(function (User $user) {
            $user->uuid = Str::uuid();

        });

        static::created(function (User $user) {
            $user->tutorials()->createMany([
                [ 'type' => Tutorial::INTRODUCTION ],
                [ 'type' => Tutorial::GAME_1       ],
                [ 'type' => Tutorial::GAME_2       ],
                [ 'type' => Tutorial::GAME_3       ],
                [ 'type' => Tutorial::GAME_4       ],
                [ 'type' => Tutorial::GAME_5       ],
                [ 'type' => Tutorial::GAME_6       ],
                [ 'type' => Tutorial::GAME_7       ],
            ]);
        });
    }

    /**
     * Get the user's friends.
     *
     * @return BelongsToMany
     */
    public function friends() : BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_friend', 'user_id', 'friend_id')
                    ->withPivot('favourite')
                    ->withTimestamps();
    }

    /**
     * Get all of the posts for the user.
     *
     * @return HasMany
     */
    public function posts() : HasMany
    {
        return $this->hasMany(Post::class);
    }

    /**
     * Get all of the comments for the user.
     *
     * @return HasMany
     */
    public function comments() : HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Get all of the tutorials for the user.
     *
     * @return HasMany
     */
    public function tutorials() : HasMany
    {
        return $this->hasMany(Tutorial::class)->orderBy('type');
    }

    /**
     * Get all of the quests for the user.
     *
     * @return HasMany
     */
    public function quests() : HasMany
    {
        return $this->hasMany(Quest::class)->orderBy('type');
    }

    /**
     * Get all of the quizzes for the user.
     *
     * @return HasMany
     */
    public function quizzes() : HasMany
    {
        return $this->hasMany(Quiz::class);
    }

    /**
     * Get all of the not seen notifications for the user.
     *
     * @return HasMany
     */
    public function notifications() : HasMany
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Get all games for the user as a host. (if any, includes finished games)
     *
     * @return ?HasMany
     */
    public function hostGames() : ?HasMany
    {
        return $this->hasMany(Game::class, 'user_id');
    }

    /**
     * Get all games for the user as a guest. (if any, includes finished games)
     *
     * @return ?BelongsToMany
     */
    public function guestGames() : ?BelongsToMany
    {
        return $this->belongsToMany(Game::class, 'game_user', 'user_id', 'game_id')
                    ->withPivot('collected_points')
                    ->withTimestamps();
    }
}
