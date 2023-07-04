<?php
/*
 * @company: 	Symbiotic Infotech Pvt. Ltd.
 * @copyright: 	Symbiotic Infotech Pvt. Ltd. 2011
 *				All rights reserved.Any redistribution or reproduction of part
 * 				or all of the contents in any form is prohibited. You may not,
 * 				except with express written permission, distribute or
 * 				commercially exploit or personally use the content.
 * 				Nor may you transmit it or store it in any other media or
 * 				other form of electronic or physical retrieval system.
 *
 * @filename:	framework.init.php
 * @filetype:	PHP
 * @filedesc:	This is the framework init to all request running through
 * 				the framework. It includes the default framework and provides
 * 				a lauch pad for most web requests
 *
 */
//register_shutdown_function('onShut');

error_reporting(7);

function onShut(){
	$error = error_get_last();
	print_r($error);
	if ($error['type'] === E_ERROR) {
		echo " fatal error has occured";
	}
}

//intialise universal variables to hold all get/post/raw post parameters
//and entry gateway
global $request, $gateway;

//start session before any other headers are sent
//but make sure we need session for this request
if ($request ['session'] == 'true')
	session_start ();

/**
 * A function to load and parse configuration values
 * into PHP constants
 * @param array $ini
 */

function loadConfiguration($file, $mod = 'framework') {
	//load the configurations file contents into the memory space
	$ini = @parse_ini_file ( $file . ".ini", true );
	if ($ini == null) {
		echo $file.chr(10);
		echo "ERROR: could not load $mod configurations into the server";
		exit ( - 1 );
	}
	//load predefined values from ini into PHP constants
	
	foreach ( $ini as $ini_key => $ini_value ) {
		foreach ( $ini [$ini_key] as $key => $value ) {
			if ($ini_key === "configuration_files") {
				//echo $key . '-' . $value . '<br />';
				loadConfiguration ( CONFIGURATION_DIR . "base/" . $value, $value );
			} else if ($ini_key === "filesystem" && $key != 'ROOT_DIR') {
				@define ( $key, $ini ['filesystem'] ['ROOT_DIR'] . $value );
				//echo $key . '-' . $ini ['filesystem'] ['ROOT_DIR'] . $value . '<br />';
			} else {
				@define ( $key, $value );
				//echo $key . '-' . $value . '<br />';
			}
		}
	}
}

//load the frame work configuration and
//the rest of the files are loaded on the fly

loadConfiguration ( "../configuration/base/framework" );
/**
 * A function used to override the include and
 * include_once functions which are very unstable
 * in a multi-site setup
 *
 * @param string $path
 */

function require_abs($path) {
	require_once ROOT_DIR . $path;
}


function require_check_abs($path) {
	if( file_exists( ROOT_DIR . $path)){
	require_once ROOT_DIR . $path;
	}
}


//Some PHP performance parameters
ini_set ( 'memory_limit', MAX_MEMORY_USAGE );
set_time_limit ( MAX_EXECUTION_TIME );
date_default_timezone_set ( DATE_TIME_ZONE );

/*require_abs ( 'framework/library/query/querybuilder.init.php' );
require_abs ( 'framework/library/log/log.init.php' );
require_abs ( 'framework/library/database/database.init.php' );
require_abs ( 'framework/library/service/service.init.php' );*/
require_abs ( 'framework/library/loadLibrary/loadLibrary.init.php' );
global $library;
$library->loadLibrary ( 'log' );
console ( LOG_LEVEL_TRACE, basename($_SERVER['PHP_SELF'])." File Initialized" );
$library->loadLibrary ( 'query' );
$library->loadLibrary ( 'db' );
$library->loadLibrary ( 'service' );
$library->loadLibrary ( 'xml' );
$library->loadLibrary ( 'curl' );
global $db;
//add entry to audit table
if ($request) {
	console ( LOG_LEVEL_TRACE, var_export ( $request, true ) );

	//$db ['master']->query ( "INSERT INTO audit (ipaddress, message) 
		//VALUES ('" . $_SERVER ['REMOTE_ADDR'] . "', '" . mysql_real_escape_string ( var_export ( $request, true ) ) . "')" );
		}

?>