<?php

/**
 * PHP version 8.1.11
 * 
 * @author Youn Mélois <youn@melois.dev>
 */

/**
 * Redirects to a new page.
 * 
 * @param string $page The requested page.
 */
function redirect(string $page): void
{
    $scheme = $_SERVER['REQUEST_SCHEME'];
    $host  = $_SERVER['HTTP_HOST'];
    $uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
    header("Location: $scheme://$host$uri/$page");
    exit;
}
