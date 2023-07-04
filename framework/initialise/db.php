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
 * @filename:	database.init.php
 * @filetype:	PHP
 * @filedesc:	this file is used to instantiate the database library
 *
 */
global $db;
try {
	$db  = new Database (DB_TELK_SUB_HOST, DB_TELK_SUB_USERNAME, DB_TELK_SUB_PASSWORD, DB_TELK_SUB_NAME );
} catch ( Exception $e ) {
	console ( LOG_LEVEL_FATAL, $e->getMessage () );
}
