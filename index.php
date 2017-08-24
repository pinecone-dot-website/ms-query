<?php

namespace Pinecone;

/**
*       PSR-4
*       @param string
*/
function autoload($class)
{
    if (strpos($class, __NAMESPACE__) !== 0) {
        return;
    }

    $file = __DIR__ .'/lib/'. str_replace('\\', '/', $class) . '.php';

    if (file_exists($file)) {
        require $file;
    }
}
spl_autoload_register( __NAMESPACE__.'\autoload' );

add_action( 'plugins_loaded', function () {
    //new MS_Query;
} );