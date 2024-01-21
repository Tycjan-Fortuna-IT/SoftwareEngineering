<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Quiz extends Model
{
    use HasFactory;


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'result',
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

        static::creating(function (Quiz $quiz) {
            $quiz->uuid = Str::uuid();
        });
    }

    /**
     * Get the user that owns the quiz.
     *
     * @return BelongsTo
     */
    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all questions for this particular quiz.
     *
     * @return BelongsToMany
     */
    public function questions() : BelongsToMany
    {
        return $this->belongsToMany(Question::class)->withPivot('answer');
    }

    /**
     * Scope a query to only include quizzes of a given user.
     *
     * @param Builder $query
     */
    public function scopeUserUuid($query, string $userUuid) : Builder
    {
        return $query->whereHas('user', function (Builder $query) use ($userUuid) {
            $query->where('uuid', $userUuid);
        });
    }
}
