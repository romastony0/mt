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
	
	function __construct($host, $dbLogin, $dbPassword, $dbName) {
		$this->host = $host;
		$this->dbLogin = $dbLogin;
		$this->dbPassword = $dbPassword;
		$this->dbName = $dbName;
		$this->connect ();
		$this->query("SET time_zone = '+5:45'");
	}
	
	function connect() {
		console ( LOG_LEVEL_TRACE, "Connecting to database. $this->dbName" );
		$link = @mysqli_connect ( $this->host, $this->dbLogin, $this->dbPassword, $this->dbName );
		if (! $link) {
			console ( LOG_LEVEL_ERROR, "Unable to connect!. $this->dbName" );
			echo mysqli_connect_error();
			throw new Exception ( "Unable to connect!". mysqli_connect_error() );
		}else{
			console ( LOG_LEVEL_ERROR, "Database connected!. $this->dbName" );
		}
		/*
		if (! mysql_select_db ( $this->dbName, $link )) {
			console ( LOG_LEVEL_ERROR, "Invalid database!. $this->dbName" );
			throw new Exception ( "Invalid database!" );
		}
		*/
		$this->link = $link;
	}
	
	function query($query) {
		console ( LOG_LEVEL_TRACE, $query );
		try {
			$results = mysqli_query ( $this->link, $query  );
			if (! $results) {
				console ( LOG_LEVEL_ERROR, "Error in query. $query . Details ".mysqli_connect_error());
				throw new Exception ( "Error in query " . $query );
			}
			return $results;
		} catch ( Exception $ex ) {
			$resultSet = $this->getOneRow('SHOW FULL PROCESSLIST');
			console ( LOG_LEVEL_ERROR, $ex->getMessage (). ' Catch Error at the time of issue ' .var_export($resultSet,true) );
		}
	}
	
	function beginTransaction() {
		console ( LOG_LEVEL_TRACE, 'Transaction started' );
		return @mysqli_query ( $this->link, 'START TRANSACTION' );
	}
	
	function commit() {
		console ( LOG_LEVEL_TRACE, 'Transaction commited' );
		return @mysqli_query ( $this->link, 'COMMIT' );
	}
	
	function rollback() {
		console ( LOG_LEVEL_TRACE, 'Transaction rollback' );
		return @mysqli_query ( $this->link, 'ROLLBACK' );
	}
	
	/**
	 * Return the last ID that was inserted.
	 */
	function getLastID() {
		return mysqli_insert_id ( $this->link );
	}
	
	/**
	 * Get a row from query as an associative array.
	 * Can be used when you are expecting only one result row.
	 *
	 * @param unknown_type $query
	 * @return unknown
	 */
	function getOneRow($query) {
		//console ( LOG_LEVEL_INFO, $query );
		try {
			$results = $this->query ( $query );
			if ($results) {
				return @mysqli_fetch_assoc ( $results );
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
		//console ( LOG_LEVEL_INFO, $qry );
		$records = array ();
		$i = 0;
		try {
			$result = $this->query ( $qry );
			if ($result) {
				$reccnt = @mysqli_num_rows ( $result );
				while ( $i < $reccnt ) {
					$res = @mysqli_fetch_assoc ( $result );
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
		return @mysqli_affected_rows ( $this->link );
	}
}

?>
