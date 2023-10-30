<?php   

namespace Documentaation\Controllers;

use OpenApi\Attributes as OA;

class AuthController
{
    #[OA\Post(
        path:'/api/register',
        summary: 'Create and register a new user',
        description: 'Create a new user and register the user into the system. // Cookie',
        tags: ['Authentication'],
        requestBody:
            new OA\RequestBody(
                required: true,
                description: 'Data needed for registration of the user',
                content: 
                    new OA\JsonContent(type: "object", required: ["nickname", "email", "password", "password_confirmation"], properties: [
                        new OA\Property(property: "nickname", type: "string", minLength: 3, maxLength: 255, examples: "coolnickname123"),
                        new OA\Property(property: "email", type: "string", maxLength: 255, examples: "jane.roe@gmail.com"),
                        new OA\Property(property: "password", type: "string", minLength: 8, maxLength: 255, examples: "strongpassword123"),
                        new OA\Property(property: "password_confirmation", type: "string", minLength: 8, maxLength: 255, examples: "strongpassword123"),
                    ])
            ),
        responses: [
            new OA\Response(
                response: '201',
                description: 'User'
            ),
            new OA\Response(
                response: '',
                description: ''
            )
        ]
    )]
    public function register() {}


    #[OA\Post(
        path:'/api/login',
        summary: '',
        tags: ['Authentication'],
        requestBody:
            new OA\RequestBody(
                required: true,
                description: '',
                content: 
                    new OA\JsonContent(type: "", required: ["", ""], properties: [
                        new OA\Property(property: "", type: "", minLength: , maxLength: , examples: ""),
                    ])
            ),
        responses: [
            new OA\Response(
                response: '',
                description: ''
            ),
            new OA\Response(
                response: '',
                description: ''
            )
        ]
    )]
    public function login() {}

    #[OA\Post(
        path:'/api/logout',
        summary: '',
        tags: ['Authentication'],
        requestBody:
            new OA\RequestBody(
                required: true,
                description: '',
                content: 
                    new OA\JsonContent(type: "", required: ["", ""], properties: [
                        new OA\Property(property: "", type: "", minLength: , maxLength: , examples: ""),
                    ])
            ),
        responses: [
            new OA\Response(
                response: '',
                description: ''
            ),
            new OA\Response(
                response: '',
                description: ''
            )
        ]
    )]
    public function logout() {}


    #[OA\Get(
        path:'/api/user',
        summary: '',
        tags: [''],
        requestBody:
            new OA\RequestBody(
                required: true,
                description: '',
                content: 
                    new OA\JsonContent(type: "", required: ["", ""], properties: [
                        new OA\Property(property: "", type: "", minLength: , maxLength: , examples: ""),
                    ])
            ),
        responses: [
            new OA\Response(
                response: '',
                description: ''
            ),
            new OA\Response(
                response: '',
                description: ''
            )
        ]
    )]
    public function user() {}


    #[OA\Get(
        path:'/api/sanctum/csrf-cookie',
        summary: '',
        tags: [''],
        requestBody:
            new OA\RequestBody(
                required: true,
                description: '',
                content: 
                    new OA\JsonContent(type: "", required: ["", ""], properties: [
                        new OA\Property(property: "", type: "", minLength: , maxLength: , examples: ""),
                    ])
            ),
        responses: [
            new OA\Response(
                response: '',
                description: ''
            ),
            new OA\Response(
                response: '',
                description: ''
            )
        ]
    )]
    public function cookie() {}

}