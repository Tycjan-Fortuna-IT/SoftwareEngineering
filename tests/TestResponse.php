<?php

namespace Tests;

use Illuminate\Support\Arr;
use Illuminate\Testing\TestResponse as BaseTestResponse;

class TestResponse extends BaseTestResponse
{
	/**
	 * Inject extension of AssertableJson.
     *
     * @return AssertableJson
	 */
	public function assertJson($value, $strict = false)
	{
		$json = $this->decodeResponseJson();

		if (is_array($value)) {
			$json->assertSubset($value, $strict);
		}
		else {
			$assert = AssertableJson::fromAssertableJsonString($json);
			$value($assert);

			if (Arr::isAssoc($assert->toArray())) {
				$assert->interacted();
			}
		}

		return $this;
	}
}