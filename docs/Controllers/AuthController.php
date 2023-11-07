<?php

namespace Docs\Controllers;

use OpenApi\Attributes as OA;

class AuthController
{
    #[OA\Post(
        path: '/api/register',
        summary: 'Create and register a new user',
        description: 'Create a new user and register the user into the system. Before sending this request, you also need to retrieve a cookie with CSRF token (see later in the section: /api/sanctum/csrf-cookie).',
        tags: ['Authentication'],
        requestBody:
            new OA\RequestBody(
                required: true,
                description: 'Data needed for registration of the user',
                content:
                    new OA\JsonContent(type: "object", required: ["name", "email", "password", "password_confirmation"], properties: [
                        new OA\Property(property: "name", type: "string", maxLength: 255, example: "coolname123"),
                        new OA\Property(property: "email", type: "string", format: "email", maxLength: 255, example: "jane.roe@gmail.com"),
                        new OA\Property(property: "password", type: "string", format: "password", minLength: 8, maxLength: 255, example: "strongpassword123"),
                        new OA\Property(property: "password_confirmation", type: "string", format: "password", minLength: 8, maxLength: 255, example: "strongpassword123"),
                    ])
            ),
        responses: [
            new OA\Response(
                response: '204',
                description: 'User has been registered successfully'
            ),
            new OA\Response(
                response: '422',
                description: 'There has been a validation error'
            )
        ]
    )]
    public function register() {}


    #[OA\Post(
        path: '/api/login',
        summary: 'Sign in to the system',
        description: 'Sign in to the system and start a session. Before sending this request, you also need to retrieve a cookie with CSRF token (see later in the section: /api/sanctum/csrf-cookie).',
        tags: ['Authentication'],
        requestBody:
            new OA\RequestBody(
                required: true,
                description: 'Data needed for signing in and starting a session',
                content:
                    new OA\JsonContent(type: "object", required: ["email", "password"], properties: [
                        new OA\Property(property: "email", type: "string", format: "email", maxLength: 255, example: "jane.roe@gmail.com"),
                        new OA\Property(property: "password", type: "string", format: "password", minLength: 8, maxLength: 255, example: "strongpassword123")
                    ])
            ),
        responses: [
            new OA\Response(
                response: '204',
                description: 'User has been signed in successfully'
            ),
            new OA\Response(
                response: '422',
                description: 'There has been a validation error'
            )
        ]
    )]
    public function login() {}

    #[OA\Post(
        path: '/api/logout',
        summary: 'Sign out of the system',
        description: 'Sign out of the system and close the session.',
        tags: ['Authentication'],
        responses: [
            new OA\Response(
                response: '204',
                description: 'User has been signed out successfully'
            )
        ]
    )]
    public function logout() {}


    #[OA\Get(
        path: '/api/user',
        summary: 'Retrieve currently signed in user',
        description: 'Retrieve details about the currently signed in user.',
        tags: ['Authentication'],
        responses: [
            new OA\Response(
                response: '200',
                description: 'User data obtained from the system',
                content:
                    new OA\JsonContent(type: "object", properties: [
                        new OA\Property(property: "data", ref: "#/components/schemas/UserResource"),
                    ])
            )
        ]
    )]
    public function user() {}


    #[OA\Get(
        path: '/api/sanctum/csrf-cookie',
        summary: 'Retrieve a cookie with CSRF token',
        tags: ['Authentication'],
        responses: [
            new OA\Response(
                response: '204',
                description: 'Cookie has been set successfully'
            ),
        ]
    )]
    public function cookie() {}

}
