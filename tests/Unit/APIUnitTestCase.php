<?php

namespace Tests\Unit;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Tests\TestCase;
use Tests\TestResponse;
use Illuminate\Support\Str;

abstract class APIUnitTestCase extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * Create a test response instance from a base response.
     *
     * @param  $response
     * @return TestResponse
     */
    protected function createTestResponse($response): TestResponse
    {
        return TestResponse::fromBaseResponse($response);
    }

    protected function get_random_uuid(): string
    {
        return Str::uuid();
    }

    protected function get_random_user(): User
    {
        return User::inRandomOrder()->first();
    }

    protected function get_random_post(): Post
    {
        return Post::inRandomOrder()->first();
    }

    protected function get_random_comment(): Comment
    {
        return Comment::inRandomOrder()->first();
    }
}