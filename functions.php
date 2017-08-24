<?php

namespace MS_Query;

/**
*
*   @return string
*/
function version()
{
    $data = get_plugin_data( __DIR__.'/_plugin.php' );
    return $data['Version'];
}
