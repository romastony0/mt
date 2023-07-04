<?php
/*
 * @company: 	Symbiotic Infotech Pvt. Ltd.
 * @copyright: 	© Symbiotic Infotech Pvt. Ltd. 2011
 *				All rights reserved.Any redistribution or reproduction of part
 * 				or all of the contents in any form is prohibited. You may not,
 * 				except with express written permission, distribute or
 * 				commercially exploit or personally use the content.
 * 				Nor may you transmit it or store it in any other media or
 * 				other form of electronic or physical retrieval system.
 *
 * @filename:	log.init.php
 * @filetype:	PHP
 * @filedesc:	this file is used to instantiate the logger library
 *
 */

global $log;

$log_directory = APPLICATION_LOG_DIR . LOG_OPERATOR_NAME. "/" .LOG_SERVICE_NAME.  "/" . date ( "Y" ) . "/" . date ( "m" ) . "/". date ( "d" ) . "/";

if (! file_exists ( $log_directory )) {
	mkdir ( $log_directory, 0777, TRUE );
}

$cfg = new SDP_Logger_Config ();
$cfg->output_dir = $log_directory;
$cfg->log_format = "tasrflnm";
$cfg->log_level = LOG_LEVEL_TRACE;
$cfg->log_separator = "|";
$cfg->log_separator_new = " "; // display some logs without seperator

/**
 * Convenience function for logging.
 *
 * @param constant $level log level
 * @param string $message message
 */
function console($level = '', $message,$newfile='') {
	global $log;
	if ($level == '' && $level != "0")
		$level = LOG_LEVEL_FATAL;
	if($newfile){	
		$filename = LOG_OPERATOR_NAME."_".LOG_SERVICE_NAME."_".date ( "Y-m-d" )."_critical_".$newfile;
	}else{
		$filename = LOG_OPERATOR_NAME."_".LOG_SERVICE_NAME."_".date ( "Y-m-d" );
	}
	$t = microtime ( true );
	$micro = sprintf ( "%06d", ($t - floor ( $t )) * 1000000 );
	$date = new DateTime ( date ( 'Y-m-d H:i:s.' . $micro, $t ) );
	$time = $date->format ( "d/m/Y H:i:s.u" );
	$log->log ( $filename, $level, ": $message", $time );
}

$log = new SDP_Logger ( $cfg );
$log->setLogLevel ( LOG_LEVEL_DEFAULT );
?>