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
 * @filename:	loadLibrary.init.php
 * @filetype:	PHP
 * @filedesc:	this file is used to instantiate the load library.
 *
 */

global $library;
require_abs ( 'framework/library/loadLibrary/loadLibrary.class.inc' );
require_abs ( 'framework/library/loadLibrary/fileLoader.class.inc' );

$library = new LoadLibrary ();
/**
 * 
 * Loading default framework inbuilt librararies.
 */
$fileLoader = new FileLoader ( 'db' );
$fileLoader->addFile ( 'framework/library/database/database.class.inc' );
$fileLoader->addFile ( 'framework/library/database/database.init.php');
$library->addLibrary ( $fileLoader );
unset($fileLoader);

$fileLoader = new FileLoader ( 'service' );
$fileLoader->addFile ( 'framework/library/service/transport.class.inc' );
$fileLoader->addFile ( 'framework/library/service/service.init.php' );
$library->addLibrary ( $fileLoader );
unset($fileLoader);

$fileLoader = new FileLoader ( 'log' );
$fileLoader->addFile ( 'framework/library/log/log.class.inc' );
$fileLoader->addFile ( 'framework/library/log/log.init.php' );
$library->addLibrary ( $fileLoader );
unset($fileLoader);

$fileLoader = new FileLoader ( 'query' );
$fileLoader->addFile ( 'framework/library/query/querybuilder.class.inc' );
$fileLoader->addFile ( 'framework/library/query/querybuilder.init.php' );
$library->addLibrary ( $fileLoader );
unset($fileLoader);

$fileLoader = new FileLoader ( 'dbvalidator' );
$fileLoader->addFile ( 'framework/library/database/dbqueryvalidator.class.inc' );
$library->addLibrary ( $fileLoader );
unset($fileLoader);

$fileLoader = new FileLoader ( 'dbqryconstructor' );
$fileLoader->addFile ( 'framework/library/database/dbqueryconstruct.class.inc' );
$library->addLibrary ( $fileLoader );
unset($fileLoader);

$fileLoader = new FileLoader ( 'xml' );
$fileLoader->addFile ( 'framework/library/xml/arraytoxml.class.inc' );
$fileLoader->addFile ( 'framework/library/xml/xmltoarray.class.inc' );
$fileLoader->addFile ( 'framework/library/xml/xml.init.php' );
$library->addLibrary ( $fileLoader );
unset($fileLoader);

$fileLoader = new FileLoader ( 'curl' );
$fileLoader->addFile ( 'framework/library/curl/curl.class.inc' );
$fileLoader->addFile ( 'framework/library/curl/curl.init.php' );
$library->addLibrary ( $fileLoader );
unset($fileLoader);

$fileLoader = new FileLoader ( 'contentMgr' );
$fileLoader->addFile ( 'application/contentmgr/model/ContentMgr.php' );
$library->addLibrary ( $fileLoader );
unset($fileLoader);

$fileLoader = new FileLoader ( 'DeflectMgr' );
$fileLoader->addFile ( 'application/deflectmgr/model/deflect.class.php' );
$library->addLibrary ( $fileLoader );
unset($fileLoader);

$fileLoader = new FileLoader ( 'PermissionMgr' );
$fileLoader->addFile("application/user/model/permission.php");
$library->addLibrary ( $fileLoader );
unset($fileLoader);
?>