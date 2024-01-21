<?php

namespace Tests\Feature\GameFeature;

use Tests\AssertableJson;

class LobbyResourceValidator
{
	public static function validate (AssertableJson $json): AssertableJson
    {
        $validatedJson = $json
            ->whereTypeUuid('uuid')
            ->whereType('limit', 'integer')
            ->whereType('level', 'integer')
            ->whereType('stage', 'integer')
            ->whereType('goal', 'integer')
            ->whereTypeTimestamp('created_at')
            ->whereTypeTimestamp('updated_at');

		return $validatedJson;
	}
}
