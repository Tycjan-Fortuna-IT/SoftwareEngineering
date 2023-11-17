<?php

namespace Tests\Unit\UserControllerTest;

use Illuminate\Http\Response;
use Tests\AssertableJson;
use Tests\Unit\APIUnitTestCase;
use Tests\Validators\LinksValidator;
use Tests\Validators\MetaValidator;

class UserControllerIndexTest extends APIUnitTestCase
{
    public function test_users_index_returns_paginated_data()
    {
        $this->actingAs($this->get_random_user())
            ->get('/api/users')
            ->assertStatus(Response::HTTP_OK)
            ->assertJson(function (AssertableJson $json) {
				$json->has('data', function (AssertableJson $data) {
					$data->each(function (AssertableJson $user) {
						UserResourceValidator::validate($user);
					});
				})
				->has('links', function (AssertableJson $links) {
					LinksValidator::validate($links);
				})
				->has('meta', function (AssertableJson $meta) {
					MetaValidator::validate($meta);
				});
			});
    }

    public function test_users_index_returns_unpaginated_data()
    {
        $this->actingAs($this->get_random_user())
            ->get('/api/users?paginate=false')
            ->assertStatus(Response::HTTP_OK)
            ->assertJson(function (AssertableJson $json) {
				$json->has('data', function (AssertableJson $data) {
					$data->each(function (AssertableJson $user) {
						UserResourceValidator::validate($user);
					});
				});
			});
    }

    public function test_users_index_returns_correct_amount_of_items_with_custom_per_page_and_page()
    {
        $response = $this->actingAs($this->get_random_user())
            ->get('/api/users?per_page=5&page=2')
            ->assertStatus(Response::HTTP_OK)
            ->assertJson(function (AssertableJson $json) {
                $json->has('data', function (AssertableJson $data) {
                    $data->each(function (AssertableJson $user) {
                        UserResourceValidator::validate($user);
                    });
                })
                ->has('links', function (AssertableJson $links) {
                    LinksValidator::validate($links);
                })
                ->has('meta', function (AssertableJson $meta) {
                    MetaValidator::validate($meta);
                });
            });

        $this->assertEquals(5, $response->json('meta.per_page'));
        $this->assertEquals(2, $response->json('meta.current_page'));
        $this->assertEquals(5, count($response->json('data')));
    }

}