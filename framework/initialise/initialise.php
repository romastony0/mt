<?php

//better start session before any other headers are sent
	if (isset($_GET ['session']) && ($_GET ['session']== 'true'))
		session_start ();
	// avoid buffering errors to the front page
	error_reporting ( 7 );

	/**
	 * A function to load and parse configuration values
	 * into PHP constants
	 * @param array $ini
	 */
		
	function loadConfiguration($file, $mod = 'framework') {
		//load the configurations file contents into the memory space
		$ini = @parse_ini_file ( $file . ".ini", true );
		if($ini==null){ 
		$file='../configuration/base/framework';
		$ini = @parse_ini_file ( $file . ".ini", true );
		}
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

	loadConfiguration ( "configuration/base/framework" );

	// Some PHP performance parameters
	ini_set ( 'memory_limit', MAX_MEMORY_USAGE );
	set_time_limit ( MAX_EXECUTION_TIME );
	date_default_timezone_set ( DATE_TIME_ZONE );

	/**
	 * A function used to override the include and
	 * include_once functions which are very unstable
	 * in a multi-site setup
	 *
	 * @param
	 *       	 $path
	 * @param
	 *       	 $root
	 */
	function require_abs($path, $root = true) {
		if ($root)
			require_once ROOT_DIR .   $path;
		else
			require_once $path;
	}
	
	function require_check_abs($path) {
	if( file_exists(ROOT_DIR . $path)){	
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
global $library,$ArrayJson;
$library->loadLibrary ( 'log' );
$library->loadLibrary ( 'query' );
$library->loadLibrary ( 'db' );
$library->loadLibrary ( 'service' );
$library->loadLibrary ( 'xml' );
global $db;
//add entry to audit table
?>


