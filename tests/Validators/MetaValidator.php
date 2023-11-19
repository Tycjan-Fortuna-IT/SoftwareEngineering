<?php

namespace Tests\Validators;

use Tests\AssertableJson;

class MetaValidator
{
    /**
     * Validate the meta object.
     *
     * @param AssertableJson $json
     * @return AssertableJson
     */
	public static function validate(AssertableJson $json) : AssertableJson
    {
		return $json
			->whereType('current_page', 'integer')
			->whereType('from', 'integer|null')
			->whereType('last_page', 'integer')
            ->whereType('per_page', 'integer')
            ->whereType('to', 'integer|null')
            ->whereType('total', 'integer')
            ->whereType('path', 'string')
			->whereType('links', 'array')
            ->has('links', function (AssertableJson $links) {
                $links->each(function (AssertableJson $link) {
                    $link->whereType('url', 'string|null')
                        ->whereType('label', 'string')
                        ->whereType('active', 'boolean');
                });
            });
	}
}