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
 * @filename:	database.class.inc
 * @filetype:	PHP
 * @filedesc:	database connector and abstraction class
 *
 */

class Database {
	
	public $host, $dbLogin, $dbPassword, $dbName;
	public $link;
	
	function Database($host, $dbLogin, $dbPassword, $dbName) {
		$this->host = $host;
		$this->dbLogin = $dbLogin;
		$this->dbPassword = $dbPassword;
		$this->dbName = $dbName;
		$this->connect ();
	}
	
	function connect() {
		console ( LOG_LEVEL_TRACE, "Connecting to database. $this->dbName" );
		$link = @mysql_connect ( $this->host, $this->dbLogin, $this->dbPassword, true );
		if (! $link) {
			console ( LOG_LEVEL_ERROR, "Unable to connect!. $this->dbName" );
			throw new Exception ( "Unable to connect!". mysql_error() );
		}
		
		if (! mysql_select_db ( $this->dbName, $link )) {
			console ( LOG_LEVEL_ERROR, "Invalid database!. $this->dbName" );
			throw new Exception ( "Invalid database!" );
		}
		
		$this->link = $link;
		$this->query("SET time_zone = '+7:00'");
	}
	
	function query($query) {
		console ( LOG_LEVEL_TRACE, $query );
		$results = mysql_query ( $query, $this->link );
		if (! $results) {
			console ( LOG_LEVEL_ERROR, "Error in query. $query . Details ".mysql_error($this->link));
			throw new Exception ( "Error in query " . $query );
		}
		return $results;
	}
	
	function beginTransaction() {
		console ( LOG_LEVEL_TRACE, 'Transaction started' );
		return @mysql_query ( 'START TRANSACTION', $this->link );
	}
	
	function commit() {
		console ( LOG_LEVEL_TRACE, 'Transaction commited' );
		return @mysql_query ( 'COMMIT', $this->link );
	}
	
	function rollback() {
		console ( LOG_LEVEL_TRACE, 'Transaction rollback' );
		return @mysql_query ( 'ROLLBACK', $this->link );
	}
	
	/**
	 * Return the last ID that was inserted.
	 */
	function getLastID() {
		return mysql_insert_id ( $this->link );
	}
	
	/**
	 * Get a row from query as an associative array.
	 * Can be used when you are expecting only one result row.
	 *
	 * @param unknown_type $query
	 * @return unknown
	 */
	function getOneRow($query) {
		try {
			$results = $this->query ( $query );
			if ($results) {
				return @mysql_fetch_assoc ( $results );
			}
		} catch ( Exception $ex ) {
			console ( LOG_LEVEL_ERROR, $ex->getMessage () );
		}
		return false;
	}
	
	/**
	 * get first field of first row for given query
	 * @param string $qr
	 * @return string/boolean
	 */
	public function getFirstField($qr) {
		$resp = $this->getOneRow ( $qr );
		if ($resp !== false) {
			return (is_array ( $resp ) && count ( $resp ) > 0) ? array_shift ( $resp ) : null;
		} else {
			return false;
		}
	}
	
	/**
	 * Get results of a query as an associative array, optionally indexed by a key field.
	 * TODO: Need better exception handling.
	 * @param string $qry Query to execute.
	 * @param string $keyfield Key field (Optional) to index the return array.
	 * @return array An associative array of data, keyed by an index count if keyfield
	 * is not provided. Otherwise indexed by the value of the key field.
	 */
	function getResults($qry, $keyfield = null) {
		$records = array ();
		$i = 0;
		try {
			$result = $this->query ( $qry );
			if ($result) {
				$reccnt = @mysql_num_rows ( $result );
				while ( $i < $reccnt ) {
					$res = @mysql_fetch_assoc ( $result );
					$key = $i;
					if ($keyfield != null)
						$key = $res [$keyfield];
					
					$records [$key] = $res;
					$i ++;
				}
			}
		} catch ( Exception $ex ) {
			console ( LOG_LEVEL_ERROR, $ex->getMessage () );
		}
		
		return $records;
	}
	
	function escapeSingleQuote($str) {
		return str_replace ( "'", "''", $str );
	}
	
	function affectedRows() {
		return @mysql_affected_rows ( $this->link );
	}
}

?>
