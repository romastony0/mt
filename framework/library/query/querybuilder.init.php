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
 * @filename:	querybuilder.init.php
 * @filetype:	PHP
 * @filedesc:	this file is used to instantiate the query builder library
 *
 */

$_POST = QueryBuilder::cleanup ( $_POST );
$_GET = QueryBuilder::cleanup ( $_GET );
$_COOKIE = QueryBuilder::cleanup ( $_COOKIE );
$_REQUEST = QueryBuilder::cleanup ( $_REQUEST );
$_SERVER ['QUERY_STRING'] = QueryBuilder::cleanup ( $_SERVER ['QUERY_STRING'] );
if(isset($_SERVER ['PATH_INFO']))
	$_SERVER ['PATH_INFO'] = QueryBuilder::cleanup ( $_SERVER ['PATH_INFO'] );
$_RAW_POST_DATA = QueryBuilder::fetchRawPostData(file_get_contents('php://input'));

QueryBuilder::buildget ();
QueryBuilder::buildpost ();
QueryBuilder::buildpathinfo ();
QueryBuilder::buildRawPost($_RAW_POST_DATA);

?>