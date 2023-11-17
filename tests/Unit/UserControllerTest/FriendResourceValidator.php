<?php

namespace Tests\Unit\UserControllerTest;

use Tests\AssertableJson;

class FriendResourceValidator
{
	public static function validate (AssertableJson $json): AssertableJson
    {
        $validatedJson = $json
            ->whereTypeUuid('uuid')
            ->whereType('name', 'string')
            ->whereType('avatar', 'string|null')
            ->whereType('email', 'string')
            ->whereType('level', 'integer')
            ->whereType('experience', 'integer')
            ->whereType('favourite', 'boolean');

		return $validatedJson;
	}
}