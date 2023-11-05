<?php

class OpenAPIExampleHelper 
{
    static $links = [
        'first' => "http://localhost:8000/api/users?page=1",
        'last' => "http://localhost:8000/api/users?page=15",
        'prev' => "http://localhost:8000/api/users?page=4",
        'next' => "http://localhost:8000/api/users?page=6",
    ];

    static public function GetLinks(): array
    {
        return self::$links;
    }


}