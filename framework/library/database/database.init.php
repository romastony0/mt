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
 * @filename:	database.init.php
 * @filetype:	PHP
 * @filedesc:	this file is used to instantiate the database library
 *
 */

global $db;
try {
	// $db ['master'] = new Database ( DB_MASTER_HOST, DB_MASTER_USERNAME, DB_MASTER_PASSWORD, DB_MASTER_NAME );
	$db['TELCO_WAP'] = new Database(DB_TELCO_WAP_HOST, DB_TELCO_WAP_USERNAME, DB_TELCO_WAP_PASSWORD, DB_TELCO_WAP_NAME);
	$db['TELCO_SUB'] = new Database(DB_TELCO_SUB_HOST, DB_TELCO_SUB_USERNAME, DB_TELCO_SUB_PASSWORD, DB_TELCO_SUB_NAME);
	$db['Agency_COMMON'] = new Database(DB_AGENCY_COMMON_HOST, DB_AGENCY_COMMON_USERNAME, DB_AGENCY_COMMON_PASSWORD, DB_AGENCY_COMMON_NAME);
	//$db ['WAP_COMMON'] = new Database ( DB_WAP_COMMON_HOST, DB_WAP_COMMON_USERNAME, DB_WAP_COMMON_PASSWORD, DB_WAP_COMMON_NAME );
	//$db ['slave'] =  $db ['master'];
} catch (Exception $e) {
	console(LOG_LEVEL_FATAL, $e->getMessage());
}
