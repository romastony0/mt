<?php
/*
 * @company: 	Symbiotic Infotech Pvt. Ltd.
 * @copyright: 	� Symbiotic Infotech Pvt. Ltd. 2011
 *				All rights reserved.Any redistribution or reproduction of part
 * 				or all of the contents in any form is prohibited. You may not,
 * 				except with express written permission, distribute or
 * 				commercially exploit or personally use the content.
 * 				Nor may you transmit it or store it in any other media or
 * 				other form of electronic or physical retrieval system.
 *
 * @filename:	log.class.inc
 * @filetype:	PHP
 * @filedesc:	This is the logging framework class to the 
 * 				entire framework, It includes the default logger configs
 * 				and provides a console/log method for all webservices
 *
 */

/**
 * Configuration class for the logger system
 *
 * Available settings:
 *
 * output_dir       - output folder for log files (REQUIRED FIELD)
 * log_level        - log level output threshold (messages lower than this won't be logged), defaults to LOG_LEVEL_ERROR
 * show_on_console  - useful for console PHP scripts, outputs log information to the console in addition to the file, defaults to false
 * log_format       - format of log file using log format markers below (defaults to "tsnlfm"
 * log_separator    - field separator used in log, defaults to "|"
 * timestamp_format - date/time format for timestamp field, defaults to "Y/m/d H:m:s"
 *
 * Log format markers:
 * t - timestamp
 * s - severity (log level) of entry
 * a - ip of the request
 * f - class and method (if called from class) or function name
 * n - name of file that called the logger
 * l - line number of file that called the logger
 * m - message
 * r - Random id for each request
 */
class SDP_Logger_Config {
	var $output_dir; // Output folder for log files
	var $log_level; // Output log level threshold
	var $show_on_console; // Output to console as well as file
	var $log_format;
	var $log_separator;
	var $timestamp_format;
	var $log_separator_new;
	/**
	 * Constructor, sets up default values for configuration
	 */
	function __construct() {
		$this->log_level = LOG_LEVEL_ERROR;
		$this->show_on_console = false;
		if (SHOW_LOG_CONSOLE == 'true')
			$this->show_on_console = true;
		$this->separate_trace_file = null;
		$this->log_format = "atsnlfm";
		$this->log_separator = "|";
		$this->log_separator_new = " "; // display some logs without seperator
		$t = microtime ( true );
		$micro = sprintf ( "%06d", ($t - floor ( $t )) * 1000000 );
		$date = new DateTime ( date ( 'Y-m-d H:i:s.' . $micro, $t ) );
		$this->timestamp_format = $date->format ( "M d Y H:i:s:u O" );
	}
}

/**
 * Main class for logger framework
 */
class SDP_Logger {
	
	var $config; // Configuration
	var $log_level_descriptions = array (LOG_LEVEL_TRACE => 'trace', LOG_LEVEL_DEBUG => 'debug', LOG_LEVEL_INFO => 'info', LOG_LEVEL_WARN => 'warning', LOG_LEVEL_ERROR => 'error', LOG_LEVEL_FATAL => 'fatal', LOG_LEVEL_ESSENTIAL => 'essential' );
	var $requestId = '';
	
	function __construct($config) {
		$this->config = $config;
		$this->requestId = md5($this->generateRandom(15, 15, false, false, true));
	}
	
	function getRequestId()
	{
		return $this->requestId;
	}
	
	function getNewTransactionId()
	{
		return md5($this->generateRandom(15, 15, false, false, true));
	}
	
	function generateRandom ($minlength, $maxlength, $uselower, $usespecial, $usenumbers)
	{
		$key='';
		$charset = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		if ($uselower) $charset .= "abcdefghijklmnopqrstuvwxyz";
		if ($usenumbers) $charset .= "0123456789";
		if ($usespecial) $charset .= "~@#$%^*()_+-={}|][";
		if ($minlength > $maxlength) $length = mt_rand ($maxlength, $minlength);
		else $length = mt_rand ($minlength, $maxlength);
		for ($i=0; $i<$length; $i++) $key .= $charset[(mt_rand(0,(strlen($charset)-1)))];
		return $key.time();
	}
	/**
	 * In particular, adjust the stack levels so that it can print values properly
	 * (useful when wrapping the calls to log)
	 */
	function log($name, $level, $data, $time = null) {
		if ($time)
			$this->config->timestamp_format = $time;
		
		if ($level >= $this->config->log_level) {
			$trace = $this->stacktrace ();
			$parts = array ();
			$index = (sizeof ( $trace ) == 1) ? 0 : 1;
			
			for($i = 0; $i < strlen ( $this->config->log_format ); $i ++) {
				$part = substr ( $this->config->log_format, $i, 1 );
				if ($part == 't')
					$parts [] = $this->config->timestamp_format;
				else if ($part == 'a') {					
					if(empty($_SERVER['REMOTE_ADDR'])) {
						$serverAddr = LOG_SELF_TRIGGERED_IP;
					} else {
						$serverAddr = $_SERVER['REMOTE_ADDR'];
					}					
					$parts [] = $serverAddr;
				}
				else if ($part == 's')
					$parts [] = " [" . $this->log_level_descriptions [$level] . "] ";
				else if ($part == 'n') {
					if (sizeof ( $trace ) == 1)
						$partswosep [] = "GLOBAL";
					else
						$partswosep [] = $trace [$index] ['name'] . "()";
				} else if ($part == 'l')
					$partswosep [] = "(" . $trace [0] ['line'] . ")"; // accurately report the line number from which logging is called.
				else if ($part == 'f')
					$partswosep [] = basename ( $trace [$index] ['file'] ); // no need of full path - clutters too much
				else if($part == 'r')
					$partswosep [] = "[".$this->requestId."]";
				else if ($part == 'm')
					$partswosep [] = $data;
				else
					$partswosep [] = $part;
			}
			$logfile = $this->config->output_dir . $name . ".log";
			$line = implode ( $this->config->log_separator, $parts );
			
			$line .= implode ( $this->config->log_separator_new, $partswosep );
			
			if (! file_exists ( $logfile)){
				$of = fopen ( $logfile, 'a' );
				$serverName = php_uname('n');
			
				if(empty($serverName)) {
					$serverName = LOG_SERVER_NAME;
				}
			
				if(empty($_SERVER['SERVER_ADDR'])) {
					$serverAddr = LOG_SERVER_IP;
				} else {
					$serverAddr = $_SERVER['SERVER_ADDR'];
				}
			
				$setFirstLine = "#".LOG_OPERATOR_NAME. "/" .LOG_SERVICE_NAME. "/" . $serverName . "/" . $serverAddr . "/" . date ( "Y-m-d" ) . "/". date ( "H:i:s" ) . "/";
				fwrite ( $of, $setFirstLine . "\r\n" );
			} else {
				$of = fopen ( $logfile, 'a' );
			}					
			
			$order   = array("\r\n", "\n", "\r","\t");
			$replace = ' ';
			if(strlen($line) > 31457280)
			{
				$line = substr($line,0,31457280).'....';
			}
			fwrite ( $of, str_replace($order,$replace,preg_replace('/^\s+|\s+$/m', '', $line)) . "\r\n" );
			fclose ( $of );
			if ($this->config->show_on_console)
				print "$line\n";
		}
	}
	
	function setLogLevel($newLevel) {
		$this->config->log_level = $newLevel;
	}
	
	/**
	 * Obtain a stack trace that doesn't include the logger functions
	 */
	function stacktrace() {
		$data = debug_backtrace ();
		array_shift ( $data ); // We don't need to report ourself
		$trace = array ();
		foreach ( $data as $d ) {
			//$cname= $d['class'];
				if (strtolower ( $d ['class'] ) != strtolower ( 'SDP_Logger' )) {
					$data = array ();
					if (isset($d ['class']) && !empty($d ['class'])) {
						$data ['name'] = $d ['class'] . $d ['type'] . $d ['function'];
					} else {
						$data ['name'] = $d ['function'];
					}
					if ($data ['name'] != "SKIP") {
						$data ['line'] = $d ['line'];
						$data ['file'] = $d ['file'];
					}
					$trace [] = $data;
				}
			
		}
		return $trace;
	}
	
	function validateIP($ip) {
		if (! empty ( $ip ) && ip2long ( $ip ) != - 1) {
			$reserved_ips = array (array ('0.0.0.0', '2.255.255.255' ), array ('10.0.0.0', '10.255.255.255' ), array ('127.0.0.0', '127.255.255.255' ), array ('169.254.0.0', '169.254.255.255' ), array ('172.16.0.0', '172.31.255.255' ), array ('192.0.2.0', '192.0.2.255' ), array ('192.168.0.0', '192.168.255.255' ), array ('255.255.255.0', '255.255.255.255' ) );
			foreach ( $reserved_ips as $r ) {
				$min = ip2long ( $r [0] );
				$max = ip2long ( $r [1] );
				if ((ip2long ( $ip ) >= $min) && (ip2long ( $ip ) <= $max))
					return false;
			}
			return true;
		} else {
			return false;
		}
	}
	
	function getRemoteIP() {
		if (self::validateIP ( $_SERVER ["HTTP_CLIENT_IP"] )) {
			return $_SERVER ["HTTP_CLIENT_IP"];
		}
		foreach ( explode ( ",", $_SERVER ["HTTP_X_FORWARDED_FOR"] ) as $ip ) {
			if (self::validateIP ( trim ( $ip ) )) {
				return $ip;
			}
		}
		if (self::validateIP ( $_SERVER ["HTTP_X_FORWARDED"] )) {
			return $_SERVER ["HTTP_X_FORWARDED"];
		} elseif (self::validateIP ( $_SERVER ["HTTP_FORWARDED_FOR"] )) {
			return $_SERVER ["HTTP_FORWARDED_FOR"];
		} elseif (self::validateIP ( $_SERVER ["HTTP_FORWARDED"] )) {
			return $_SERVER ["HTTP_FORWARDED"];
		} elseif (self::validateIP ( $_SERVER ["HTTP_X_FORWARDED"] )) {
			return $_SERVER ["HTTP_X_FORWARDED"];
		} else {
			return $_SERVER ["REMOTE_ADDR"];
		}
	}

}
?>
