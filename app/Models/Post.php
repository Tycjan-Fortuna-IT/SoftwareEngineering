<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;
use \Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Post extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'image',
        'title',
        'description',
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
     * Standard boot function for defining proper action handling.
     *
     * @return void
     */
    public static function boot() : void
    {
        parent::boot();

        static::creating(function (Post $post) {
            $post->uuid = Str::uuid();
        });
    }

    /**
     * Get the user that owns the post.
     *
     * @return BelongsTo
     */
    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all of the comments for the post.
     *
     * @return HasMany
     */
    public function comments() : HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Scope a query to only include posts of a given user.
     *
     * @param string $uuid
     * @return Builder
     */
    public function scopeUserUuid($query, string $uuid) : Builder
    {
        return $query->whereHas('user', function ($query) use ($uuid) {
            $query->where('uuid', $uuid);
        });
    }

    /**
     * Get all posts that match a given search title.
     *
     * @param string $uuid
     * @return Builder
     */
    public function scopeSearch($query, string $search) : Builder
    {
        return $query->where('title', 'LIKE', '%' . $search . '%');
    }
}
