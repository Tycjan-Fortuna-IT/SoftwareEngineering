<?php

namespace Tests\Unit\PostControllerTest;

use Tests\AssertableJson;

class CommentInPostResourceValidator
{
	public static function validate (AssertableJson $json): AssertableJson
    {
        $validatedJson = $json
            ->whereTypeUuid('uuid')
            ->whereType('content', 'string');

		return $validatedJson;
	}
}
