<?php
/*
Plugin Name: barefoot
Plugin URI: 
Description: 
Author: 
Version: 
Author URI: 

This file must be parsable by php 5.2
*/

register_activation_hook( __FILE__, create_function("", '$ver = "5.3"; if( version_compare(phpversion(), $ver, "<") ) die( "This plugin requires PHP version $ver or greater be installed." );') );

require __DIR__.'/index.php';
