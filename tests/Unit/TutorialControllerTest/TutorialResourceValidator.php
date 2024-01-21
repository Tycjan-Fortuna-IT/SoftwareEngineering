<?php

namespace Tests\Unit\TutorialControllerTest;

use Tests\AssertableJson;

class TutorialResourceValidator
{
	public static function validate (AssertableJson $json): AssertableJson
    {
        $validatedJson = $json
            ->whereTypeUuid('uuid')
            ->whereType('name', 'string')
            ->whereType('type', 'integer')
            ->whereType('completed', 'boolean')
            ->whereTypeTimestamp('created_at')
            ->whereTypeTimestamp('updated_at');

		return $validatedJson;
	}
}
