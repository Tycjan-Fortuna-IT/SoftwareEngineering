<?php

namespace Tests\Unit\UserControllerTest;

use Tests\AssertableJson;

class UserResourceValidator
{
	public static function validate (AssertableJson $json): AssertableJson
    {
        $validatedJson = $json
            ->whereTypeUuid('uuid')
            ->whereType('name', 'string')
            ->whereType('avatar', 'string|null')
            ->whereType('about', 'string|null')
            ->whereType('email', 'string')
            ->whereType('level', 'integer')
            ->whereType('experience', 'integer')
            ->whereType('anonymous', 'boolean')
            ->has('friends', function (AssertableJson $friends) {
                $friends->eachNullable(function (AssertableJson $friend) {
                    FriendResourceValidator::validate($friend);
                });
            })
            ->whereTypeTimestamp('created_at')
            ->whereTypeTimestamp('updated_at');

		return $validatedJson;
	}
}