<?php

/**
 * Strip the APP_URL path prefix from REQUEST_URI when WARCC runs in a subdirectory.
 *
 * Example: APP_URL=https://cbp.africacdc.org/warcc and REQUEST_URI=/warcc/about
 * becomes REQUEST_URI=/about so Laravel routes match.
 */
function warcc_strip_subdirectory_prefix_from_request(): void
{
    if (! isset($_SERVER['REQUEST_URI'])) {
        return;
    }

    $appUrl = $_ENV['APP_URL'] ?? getenv('APP_URL') ?: '';
    if ($appUrl === '') {
        return;
    }

    $basePath = parse_url($appUrl, PHP_URL_PATH);
    if (! is_string($basePath) || $basePath === '' || $basePath === '/') {
        return;
    }

    $requestUri = $_SERVER['REQUEST_URI'];
    if (! str_starts_with($requestUri, $basePath)) {
        return;
    }

    $remainder = substr($requestUri, strlen($basePath));
    $_SERVER['REQUEST_URI'] = ($remainder === '' || $remainder === false) ? '/' : $remainder;
}
