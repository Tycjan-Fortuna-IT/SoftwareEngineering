<?php

namespace Tests\Unit\TutorialControllerTest;

use Illuminate\Http\Response;
use Tests\AssertableJson;
use Tests\Unit\APIUnitTestCase;

class TutorialControllerIndexTest extends APIUnitTestCase
{
    public function test_users_index_returns_paginated_data()
    {
        $this->actingAs($this->get_random_user())
            ->get('/api/tutorials')
            ->assertStatus(Response::HTTP_OK)
            ->assertJson(function (AssertableJson $json) {
				$json->has('data', function (AssertableJson $data) {
					$data->each(function (AssertableJson $user) {
						TutorialResourceValidator::validate($user);
					});
				});
			});
    }
}
