<?php

namespace Tests\Unit\CommentControllerTest;

use Tests\AssertableJson;

class UserInCommentResourceValidator
{
	public static function validate (AssertableJson $json): AssertableJson
    {
        $validatedJson = $json
            ->whereTypeUuid('uuid')
            ->whereType('name', 'string')
            ->whereType('avatar', 'string|null')
            ->whereType('email', 'string')
            ->whereType('level', 'integer');

		return $validatedJson;
	}
}
