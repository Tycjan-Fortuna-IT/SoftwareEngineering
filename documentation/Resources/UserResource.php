<?php 

namespace Documentation\Resources;

use OpenApi\Attributes as OA;

#[OA\Schema(
    title: "UserResource",
    type: "object",
    description: "Registered user that uses the system"
)]

class UserResource
{
    #[OA\Property(property: "uuid", type: "string", format: "uuid", example: "04776536-c79c-4baa-a3ba-db945d70c902")]
    #[OA\Property(property: "name", type: "string", maxLength: 255, example: "Jane Roe")]
    #[OA\Property(property: "email", type: "string", format: "email", maxLength: 255, example: "jane.roe@gmail.com")]
    #[OA\Property(property: "created_at", type: "string", format: "date-time", example: "2023-12-31T15:39:57+00:00")]
    #[OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2023-12-31T15:39:57+00:00")]
    public function generate() {}
}
