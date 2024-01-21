<?php

namespace Tests\Unit\CommentControllerTest;

use Tests\AssertableJson;

class CommentResourceValidator
{
	public static function validate (AssertableJson $json): AssertableJson
    {
        $validatedJson = $json
            ->whereTypeUuid('uuid')
            ->whereType('content', 'string')
            ->hasNullable('user', function (AssertableJson $user) {
                UserInCommentResourceValidator::validate($user);
            });

		return $validatedJson;
	}
}
