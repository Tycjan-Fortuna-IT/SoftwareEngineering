<?php

namespace Tests\Validators;

use Tests\AssertableJson;

class LinksValidator
{
    /**
     * Validate the links object.
     *
     * @param AssertableJson $json
     * @return AssertableJson
     */
	public static function validate (AssertableJson $json) : AssertableJson
    {
      return $json
        ->whereType('first', 'string')
        ->whereType('last', 'string')
        ->whereType('prev', 'string|null')
        ->whereType('next', 'string|null');
	}
}