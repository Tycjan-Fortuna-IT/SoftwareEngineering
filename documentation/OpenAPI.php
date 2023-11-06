<?php

namespace Documentation;

class OpenAPIExampleHelper 
{
    const LINKS = [
        'first' => "http://localhost:8000/api/users?page=1",
        'last' => "http://localhost:8000/api/users?page=15",
        'prev' => "http://localhost:8000/api/users?page=4",
        'next' => "http://localhost:8000/api/users?page=6",
    ];
}

use OpenApi\Attributes as OA;

require(__DIR__ . '/requires.php');

#[OA\Info(title: "Software Engineering API", version: "1.0.0")]
#[OA\Server(url: "https://se-test-server.it-core.fun", description: "Test server for SE 2023/2024")]
#[OA\Contact(email: "242213@edu.p.lodz.pl")]
#[OA\OpenApi(openapi: "3.0.0")]
class OpenApi {}

#[OA\Schema(
    title: "LinksResponsePart",
    type: "object",
    description: "A part of successfully paginated response that contains links to other pages."
)]
class LinksResponsePart
{
    #[OA\Property(property: "first", type: "string", example: "http://localhost:8000/api/users?page=1")]
    #[OA\Property(property: "last", type: "string", example: "http://localhost:8000/api/users?page=15")]
    #[OA\Property(property: "prev", type: "string", example: "http://localhost:8000/api/users?page=4")]
    #[OA\Property(property: "next", type: "string", example: "http://localhost:8000/api/users?page=6")]
    public function generate() {}
}

#[OA\Schema(
    title: "MetaResponsePart",
    type: "object",
    description: "A part of successfully paginated response that contains metadata."
)]
class MetaResponsePart
{
    #[OA\Property(property: "current_page", type: "integer", example: 1)]
    #[OA\Property(property: "from", type: "integer", example: 1)]
    #[OA\Property(property: "last_page", type: "integer", example: 15)]
    #[OA\Property(property: "per_page", type: "integer", example: 15)]
    #[OA\Property(property: "to", type: "integer", example: 15)]
    #[OA\Property(property: "total", type: "integer", example: 15)]
    #[OA\Property(property: "path", type: "string", example: "http://localhost:8000/api/users")]
    #[OA\Property(property: "links", type: "array", items:
        new OA\Items(properties: [
            new OA\Property(property: "url", type: "string", example: "http://localhost:8000/api/users?page=1"),
            new OA\Property(property: "label", type: "string", example: "1"),
            new OA\Property(property: "active", type: "boolean", example: true),
        ])
    )]
    public function generate() {}
}

#[OA\Parameter(
    parameter: "uuid_url_param",
    name: "uuid",
    in: "path",
    required: true,
    schema: new OA\Schema(type: "string", format: "uuid", example: "04776536-c79c-4baa-a3ba-db945d70c902"),

)]
class UuidPathParam {}

#[OA\Parameter(
    parameter: "filter_by_user_uuid_param",
    name: "filter[user_uuid]",
    description: "Allows for filtering resources by the UUID of the user",
    in: "query",
    required: false,
    schema: new OA\Schema(type: "string", format: "uuid", example: "04776536-c79c-4baa-a3ba-db945d70c902"),

)]
class FilterByUserUuidQueryParam {}

#[OA\Parameter(
    parameter: "filter_by_user_uuid_param_required",
    name: "filter[user_uuid]",
    description: "Allows for filtering resources by the UUID of the user",
    in: "query",
    required: true,
    schema: new OA\Schema(type: "string", format: "uuid", example: "04776536-c79c-4baa-a3ba-db945d70c902"),

)]
class FilterByUserUuidRequiredQueryParam {}

#[OA\Parameter(
    parameter: "filter_by_type_param",
    name: "filter[type]",
    description: "Allows for filtering resources by their exact type",
    in: "query",
    required: false,
    schema: new OA\Schema(type: "integer", format: "int32", example: 1),

)]
class FilterByTypeQueryParam {}

#[OA\Parameter(
    name: "page",
    in: "query",
    required: false,
    description: "If pagination is enabled, returns a specified page of the data resources",
    schema: new OA\Schema(type: "integer", format: "int32", default: 1)
)]
class PageNumberQueryParam {}

#[OA\Parameter(
    name: "per_page",
    in: "query",
    required: false,
    description: "If pagination is enabled, returns a specified number of data resources per page",
    schema: new OA\Schema(type: "integer", format: "int32", default: 15)
)]
class PerPageQueryParam {}

#[OA\Parameter(
    name: "paginate",
    in: "query",
    required: false,
    description: "If pagination is enabled, returns paginated data resources",
    schema: new OA\Schema(type: "boolean", default: true)
)]
class DoPaginateQueryParam {}