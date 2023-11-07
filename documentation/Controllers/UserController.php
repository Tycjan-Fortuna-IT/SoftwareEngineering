<?php

namespace Documentation\Controllers;

use OpenApi\Attributes as OA;

class UserController
{
    #[OA\Get(
        path: '/api/users',
        summary: 'Retrieve all users',
        description: 'Retrieve all users. Depending on whether the query parameters have been entered, obtain paginated or unpaginated response.',
        tags: ['User'],
        parameters: [
            new OA\Parameter(ref: "#/components/parameters/page"),
            new OA\Parameter(ref: "#/components/parameters/per_page"),
            new OA\Parameter(ref: "#/components/parameters/paginate")
        ],
        responses: [
            new OA\Response(
                response: '200',
                description: 'Users have been retrieved successfully',
                content: 
                    new OA\JsonContent(type: "object", examples: [
                        new OA\Examples(
                            summary: "Paginated response",
                            example: "Paginated response",
                            value: [
                                'data' => \Documentation\OpenAPIExampleHelper::EXAMPLE_USER_DATA,
                                'links' => \Documentation\OpenAPIExampleHelper::EXAMPLE_LINKS,
                                'meta' => \Documentation\OpenAPIExampleHelper::EXAMPLE_META
                            ]
                        ),
                        new OA\Examples(
                            summary: "Not paginated response",
                            example: "Not paginated response",
                            value: [
                                'data' => \Documentation\OpenAPIExampleHelper::EXAMPLE_USER_DATA
                            ]
                        )
                    ])

            )
        ]
    )]
    public function index() {}

    #[OA\Get(
        path: '/api/users/{uuid}',
        summary: 'Retrieve a user',
        description: 'Retrieve the data of a user specified by UUID.',
        tags: ['User'],
        parameters: [
            new OA\Parameter(ref: "#/components/parameters/uuid_url_param")
        ],
        responses: [
            new OA\Response(
                response: '200',
                description: 'The user has been retrieved successfully',
                content: [
                    new OA\JsonContent(type: "object", properties: [
                        new OA\Property(
                            property: "data",
                            ref: "#/components/schemas/UserResource"
                        )
                    ])
                ]
            ),
            new OA\Response(
                response: '404',
                description: 'User has not been found',
            )
        ]
    )]
    public function show() {}

    #[OA\Put(
        path: '/api/users/{uuid}',
        summary: 'Update a user',
        description: 'Update the data of a user specified by UUID.',
        tags: ['User'],
        parameters: [
            new OA\Parameter(ref: "#/components/parameters/uuid_url_param")
        ],
        requestBody:
            new OA\RequestBody(
                required: true,
                description: 'User data required for the update',
                content: [
                    new OA\JsonContent(type: "object", properties: [
                        new OA\Property(property: "name", type: "string", maxLength: 255, example: "coolname123"),
                        new OA\Property(property: "email", type: "string", format: "email", maxLength: 255, example: "jane.roe@gmail.com"),
                        new OA\Property(property: "password", type: "string", minLength: 8, maxLength: 255, example: "strongpassword123"),
                        new OA\Property(property: "password_confirmation", type: "string", minLength: 8, maxLength: 255, example: "strongpassword123"),
                    ])
                ]
            ),
        responses: [
            new OA\Response(
                response: '200',
                description: 'User has been updated successfully',
            ),
            new OA\Response(
                response: '404',
                description: 'User has not been found',
            ),
            new OA\Response(
                response: '422',
                description: 'There has been a validation error',
            )
        ]
    )]
    public function update() {}

    #[OA\Delete(
        path: '/api/users/{uuid}',
        summary: 'Delete a user',
        description: 'Delete a user specified by UUID.',
        tags: ['User'],
        parameters: [
            new OA\Parameter(ref: "#/components/parameters/uuid_url_param")
        ],
        responses: [
            new OA\Response(
                response: '200',
                description: 'User has been deleted successfully',
            ),
            new OA\Response(
                response: '404',
                description: 'User has not been found',
            )
        ]
    )]
    public function delete() {}

}