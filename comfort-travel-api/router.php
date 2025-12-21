<?php
$request_uri = $_SERVER['REQUEST_URI'];
$script_name = $_SERVER['SCRIPT_NAME'];

// Remove script name from request URI
$path = parse_url($request_uri, PHP_URL_PATH);
$path = str_replace(dirname($script_name), '', $path);

// Route the request
if (file_exists('.' . $path)) {
    // Serve the file directly
    return false;
} else {
    // Route to index.php
    include 'index.php';
}