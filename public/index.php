<?php

use App\Kernel;

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

$environment = $_SERVER['APP_ENV'] ?? 'dev';
if (!is_string($environment)) {
    // we expect the environment variable to be a string, if not, we throw an error
    // Symfony's default cast non-string APP_ENV to string, but I changed it (mainly for PHPStan checks)
    exit(sprintf('Error: APP_ENV environment variable must be a string, but got %s.', get_debug_type($environment)));
}

return function (array $context) use ($environment) {
    return new Kernel($environment, (bool) $context['APP_DEBUG']);
};
