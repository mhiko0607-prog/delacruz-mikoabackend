<?php

$public_dir = realpath(__DIR__ . DIRECTORY_SEPARATOR . 'public');
$request_path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$request_path = is_string($request_path) ? rawurldecode($request_path) : '/';

$ui_prefix = '/lavalustui';
if ($request_path === $ui_prefix || strpos($request_path, $ui_prefix . '/') === 0) {
    $ui_dir = realpath(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'lavalustui');
    $ui_path = ltrim(substr($request_path, strlen($ui_prefix)), '/');
    $ui_file = realpath(
        $ui_dir . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $ui_path)
    );

    if ($ui_file !== false && is_dir($ui_file)) {
        $ui_file = realpath($ui_file . DIRECTORY_SEPARATOR . 'index.html');
    }

    if (
        $ui_dir !== false
        && $ui_file !== false
        && is_file($ui_file)
        && strpos($ui_file, $ui_dir . DIRECTORY_SEPARATOR) === 0
    ) {
        $extension = strtolower(pathinfo($ui_file, PATHINFO_EXTENSION));
        $mime_types = [
            'css' => 'text/css; charset=utf-8',
            'html' => 'text/html; charset=utf-8',
            'js' => 'application/javascript; charset=utf-8',
        ];
        header('Content-Type: ' . ($mime_types[$extension] ?? 'application/octet-stream'));
        readfile($ui_file);
        exit;
    }

    http_response_code(404);
    exit;
}

$requested_file = realpath(
    $public_dir . DIRECTORY_SEPARATOR . ltrim(str_replace('/', DIRECTORY_SEPARATOR, $request_path), DIRECTORY_SEPARATOR)
);

if (
    $request_path !== '/'
    && $requested_file !== false
    && is_file($requested_file)
    && strpos($requested_file, $public_dir . DIRECTORY_SEPARATOR) === 0
) {
    return false;
}

require $public_dir . DIRECTORY_SEPARATOR . 'index.php';
