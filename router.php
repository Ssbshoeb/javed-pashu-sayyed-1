<?php
/*
|--------------------------------------------------------------------------
| Development router
|--------------------------------------------------------------------------
| Only for PHP's built-in server, which has no .htaccess:
|
|   php -S localhost:8000 router.php
|
| It does what the rewrite rules do — serve a real file if there is one, send
| everything else to the front controller. Nothing on the live host uses it,
| and it can be deleted before uploading if you would rather not ship it.
*/

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$file = __DIR__ . $path;

// Never serve the application's own source or its storage, even locally: the
// point of testing is to test what will be deployed.
if (preg_match('#^/(app|storage)(/|$)#', (string) $path) || str_starts_with(basename((string) $path), '.')) {
    http_response_code(403);
    echo 'Forbidden';
    return true;
}

if ($path !== '/' && is_file($file)) {
    // false tells the built-in server to serve the file itself, with the right
    // Content-Type.
    return false;
}

require __DIR__ . '/index.php';
