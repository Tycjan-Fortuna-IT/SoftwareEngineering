<?php

namespace Docs;

require("vendor/autoload.php");
require("docs/OpenAPI.php");

// during the invocation of the script, a path to the docs folder should be specified
// eg. php .\docs\Generator.php C:\SoftwareEngineering\docs
$openapi = \OpenApi\Generator::scan([$argv[1]]);

header('Content-Type: application/x-yaml');

file_put_contents('docs/openapi.yaml', $openapi->toYaml());
