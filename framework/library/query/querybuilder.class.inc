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
 * @filename:	queryname.class.inc
 * @filetype:	PHP
 * @filedesc:	this file is used to build the global request varaible from post,get,path info
 *
 */

class QueryBuilder {
	/**
	 * A function used to build the request variable from $_POST
	 * @param none
	 * @return none
	 */
	public static function buildpost() {
		global $request;
		foreach ( $_POST as $post_key => $post_value ) {
			$request [$post_key] = $post_value;
		}
		unset ( $_POST );
	}
	/**
	 * A function used to build the request variable
	 * @param none
	 * @return none
	 */
	public static function buildget() {
		global $request;
		foreach ( $_GET as $get_key => $get_value ) {
			$request [$get_key] = $get_value;
		}
		unset ( $_GET );
	}
	
	/**
	 * A function used to build the request variable
	 * @param none
	 * @return none
	 */
	public static function buildpathinfo() {
		if(isset($_SERVER ['PATH_INFO']))
		{
			$_PATH_INFO = preg_split ( '/\//', $_SERVER ['PATH_INFO'], - 1, PREG_SPLIT_NO_EMPTY );
			foreach ( $_PATH_INFO as $req_key => $req_value ) {
				$_PATH_INFO [$req_key] = preg_split ( '/:/', $_PATH_INFO [$req_key], - 1, PREG_SPLIT_NO_EMPTY );
				if ($_PATH_INFO [$req_key] [0] && $_PATH_INFO [$req_key] [1])
					$request [$_PATH_INFO [$req_key] [0]] = $_PATH_INFO [$req_key] [1];
			}
			unset ( $_SERVER ['PATH_INFO'] );
		}
	}
	
	/**
	 * A function used to build the request variable
	 * @param none
	 * @return none
	 */
	public static function cleanup($myinput) {
		if (is_array ( $myinput )) {
			foreach ( $myinput as $key => $val ) {
				$myinput [$key] = QueryBuilder::cleanvals ( $val );
				$myinput [$key] = QueryBuilder::cleansql ( $myinput [$key] );
			}
			return $myinput;
		} else {
			$myinput = QueryBuilder::cleanvals ( $myinput );
			return QueryBuilder::cleansql ( $myinput );
		}
	}
	
	/**
	 * A function used to cleans the variables
	 * @param $myval
	 * @return $myval
	 */
	public static function cleanvals($myval) {
		if (is_array ( $myval )) {
			foreach ( $myval as $key => $val ) {
				$myval [$key] = QueryBuilder::cleanvals ( $val );
			}
			return $myval;
		}
		
		$badbadsql = array ('(declare)', '(cast)', '/(drop)/' );
		$myval = preg_replace ( $badbadsql, "", $myval );
		if (function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc()) {
			//get rid of triple slashes mysql_real_escape_string would create
			$myval = stripslashes ( $myval );
		}
		return $myval;
	}
	
	/**
	 * A function used to cleans the sql injection
	 * @param $str
	 * @return $myval
	 */
	public static function cleansql($str) {
		$characters = array ('/\x00/', '/\x1a/', '/\\\/', '/\'/' );
		$replace = array ('\\\x00', '\\x1a', '\\\\', "''" );
		return preg_replace ( $characters, $replace, $str );
	}
	
	public static function fetchRawPostData($RawPostData){
		global $library;
		if(!empty($RawPostData)){
			$raw_post_array = json_decode($RawPostData,true);
			if($raw_post_array){
				return QueryBuilder::cleanup($raw_post_array);
			}else{
				//check for XML.
				global $libxml;
				$library->loadLibrary('xml');
				$raw_post_array = $libxml->getArray($RawPostData);
				if($raw_post_array){
					return QueryBuilder::cleanup($raw_post_array);	
				}else{
					return "";
				}	
			}
		}else{
			return "";
		}
	}
	
	public static function buildRawPost($raw_data){
		global $request;
		if($raw_data){
			foreach ( $raw_data as $get_key => $get_value ) {
				$request [$get_key] = $get_value;
			}
		}else{
			return false;
		}
	}
}
?>