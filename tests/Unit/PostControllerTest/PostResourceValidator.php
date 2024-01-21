<?php

namespace Tests\Unit\PostControllerTest;

use Tests\AssertableJson;

class PostResourceValidator
{
	public static function validate (AssertableJson $json): AssertableJson
    {
        $validatedJson = $json
            ->whereTypeUuid('uuid')
            ->whereType('image', 'string|null')
            ->whereType('title', 'string')
            ->whereType('description', 'string')
            ->hasNullable('user', function (AssertableJson $user) {
                UserInPostResourceValidator::validate($user);
            })
            ->hasNullable('comments', function (AssertableJson $comments) {
                $comments->each(function (AssertableJson $comment) {
                    CommentInPostResourceValidator::validate($comment);
                });
            })
            ->whereTypeTimestamp('created_at')
            ->whereTypeTimestamp('updated_at');

		return $validatedJson;
	}
}
