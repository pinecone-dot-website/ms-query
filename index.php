<?php

namespace MS_Query;

if (file_exists(__DIR__.'/vendor/autoload.php')) {
    require __DIR__.'/vendor/autoload.php';
}

if (!function_exists('/MS_Query/version')) {
    require __DIR__.'/autoload.php';
}

add_action( 'plugins_loaded', function () {
    //new MS_Query;
} );
