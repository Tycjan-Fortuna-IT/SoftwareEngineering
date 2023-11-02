<?php

namespace Documentation;

require("vendor/autoload.php");
require("documentation/OpenAPI.php");

// during the invocation of the script, a path to the documentation folder should be specified
// eg. php .\documentation\Generator.php C:\SoftwareEngineering\documentation
$openapi = \OpenApi\Generator::scan([$argv[1]]);

header('Content-Type: application/x-yaml');

file_put_contents('documentation/openapi.yaml', $openapi->toYaml());