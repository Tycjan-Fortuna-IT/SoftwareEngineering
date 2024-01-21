<?php

namespace Tests\Unit\PostControllerTest;

use Tests\AssertableJson;

class UserInPostResourceValidator
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
