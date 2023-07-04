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
 * @filename:	queryconstruct.php
 * @filetype:	PHP
 * @filedesc:   This file is used for creating Database Query to insert,update,delete and select.
 * 
 *
 */

class QueryConstruct{
	
	public function __construct(){}
	/**
	 * generate insert query for db
	 * @param unknown_type $obj bean class object
	 * @return string|boolean
	 */
	public static function insertQuery($obj){
		
		//log_twm(TWM_LOGLEVEL_INFO, "executing ".__METHOD__);
		if(method_exists($obj,'getData')){
			$data = array();$data = $obj->getData();
			vaR_dump($data);
			$keys_values=array();
			if(count($data)>0){
				foreach($data as $key=>$val){
					if(isset($val)){
						$keys_values[$key]=$val;	
					} 
				}
				$query = sprintf("INSERT INTO %s (%s) VALUES ('%s');", get_class($obj), implode(', ', array_map('mysql_real_escape_string', array_keys($keys_values))), implode("', '",array_map('mysql_real_escape_string', $keys_values)));
				return $query;
			}else{	
				$errmsg =  "insert data array is empty";
			}
		}else{	
			$errmsg = "method doesn't exist in object";		
		}
		var_Dump($errmsg);
		if(isset($errmsg)){
			//log_twm(TWM_LOGLEVEL_ERROR, $errmsg);
			return false;
		}
	}
	/**
	 * generate update query for db
	 * @param unknown_type $obj bean class object
	 * @param unknown_type $where condition
	 * @return string|boolean
	 */
	public static function updateQuery($obj,$where=null){
		//log_twm(TWM_LOGLEVEL_INFO, "executing ".__METHOD__);
		if(method_exists($obj,'getData')){
			$data = array();$data = $obj->getData();
			if(count($data)>0){
				$classname = get_class($obj);
				// generate sql query for updation
				$query = "UPDATE $classname SET ";
				foreach($data as $key=>$value){
					if(is_null($value))
						continue;
					//check query execute safe setting
					$key = mysql_real_escape_string($key);
					$value = mysql_real_escape_string($value);
					$query .= "$classname.$key = '$value', ";
				}
				$query = substr($query,0,-2); //remove last 2 characters(, )
				if(!is_null($where) and is_string($where))	//where is set
					$query .= ",date=CURRENT_TIMESTAMP() WHERE $where";
				$query .= ";";
				return $query;
			}else{	
				$errmsg =  "update data array is empty";
			}
		}else{	
			$errmsg = "method doesn't exist in object";		
		}
		
		if(isset($errmsg)){
			//log_twm(TWM_LOGLEVEL_ERROR, $errmsg);
			return false;
		}
	}
	/**
	 * generate delete query for db
	 * @param unknown_type $obj bean class object
	 * @param unknown_type $where condition
	 * @return string|boolean
	 */
	/*public static function deleteQuery($obj,$where=null){
		//log_twm(TWM_LOGLEVEL_INFO, "executing ".__METHOD__);
		if(is_object($obj)){
			// generate sql query for deletion
			$query = "DELETE FROM ".get_class($obj);
			if(!is_null($where) and is_string($where))	//where is set
				$query .= " WHERE $where";
			$query .= ";";
			return $query;
		}else{	
			$errmsg = "delete object doesn't exist";		
		}
		
		if(isset($errmsg)){
			//log_twm(TWM_LOGLEVEL_ERROR, $errmsg);
			return false;
		}
	}*/
	
	public static function deleteQuery($obj,$where=null){
		
		if(method_exists($obj,'getData')){
			$data = array();$data = $obj->getData();
			if(count($data)>0){
				$classname = get_class($obj);
				// generate sql query for updation
				$query = "UPDATE $classname SET status='deleted', date=CURRENT_TIMESTAMP()";
				if(!is_null($where) and is_string($where))	//where is set
					$query .= " WHERE $where";
				$query .= ";";
				return $query;
			}else{	
				$errmsg =  "update data array is empty";
			}
		}else{	
			$errmsg = "method doesn't exist in object";		
		}
		
		if(isset($errmsg)){
			//log_twm(TWM_LOGLEVEL_ERROR, $errmsg);
			return false;
		}
	}
	
	/**
	 * generate select query
	 * @param $obj bean class object
	 * @param $where condition
	 * @param $limit result limit
	 * @return string|boolean
	 */
	public static function selectQuery($obj,$where=null,$limit=0){
		//log_twm(TWM_LOGLEVEL_INFO, "executing ".__METHOD__);
		if(is_object($obj)){
			// generate sql query for selection
			$query = "SELECT * FROM ".get_class($obj);
			if(!is_null($where) and is_string($where))	//where is set
				$query .= " WHERE $where";
			if($limit)
				$query .= " LIMIT $limit";
			$query .= ";";
			return $query;
		}else{	
			$errmsg = "select object doesn't exist";		
		}
				
		if(isset($errmsg)){
			//log_twm(TWM_LOGLEVEL_ERROR, $errmsg);
			return false;
		}
	}
}