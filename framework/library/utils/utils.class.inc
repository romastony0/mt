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
 * @filename:	utils.class.inc
 * @filetype:	PHP
 * @filedesc:	Utility file for handling Utility functions.
 *
 */

class Utils{
	
	public $bean_obj=null;
	public $service_query_construct=null;
	public $where=null;
	public $limit=null;
	public $extra_where_clause=null;
	public $query=null;
	
	public function Utils(){
			$this->bean_obj = null;
			$this->where = null;
			$this->limit = null;
			$this->extra_where_clause = null;
			$this->query = null;
			$this->createQueryConstructObj();
	}
	
	public function addAction(){
		console(LOG_LEVEL_TRACE,"Inside the addService Function.");
		global $request,$db;
		if(method_exists($this->service_query_construct, 'insertQuery')){
			console(LOG_LEVEL_TRACE,"Calling function to Create Insert Query.");
			$this->query = $this->service_query_construct->insertQuery($this->bean_obj);
			if($this->query){
				console(LOG_LEVEL_TRACE,"Calling function query() to execute insert query.");
				$sqlstatus = $db['master']->query($this->query);
				if($sqlstatus){
					$lastid = $db['master']->getLastID();
					if(!$lastid){
						return false;
					}
					console(LOG_LEVEL_TRACE,"Calling function selectQuery() to fetch the last inserted record.");
					$this->query = $this->service_query_construct->selectQuery($this->bean_obj,$this->extra_where_clause.$lastid."'",1);
					if($this->query){
						console(LOG_LEVEL_TRACE,"Fetching last row inserted.");
						$resultdata = $db['master']->getOneRow($this->query);
						if($resultdata){
							return $resultdata;
						}else{
							console(LOG_LEVEL_TRACE,"Not able to fetch last row inserted.");
							return false;
						}
					}else{
						console(LOG_LEVEL_TRACE,"Query returned from selectQuery() is blank.");
						return false;
					}
				}else{
					console(LOG_LEVEL_TRACE,"Not able to insert a data.");
					return false;
				}
			}else{
				console(LOG_LEVEL_TRACE,"Not able to genrate a insert query.");
				return false;
			}
		}else{
			print "Insert Function not present in $this->service_query_construct Class";
			console(LOG_LEVEL_TRACE,"Insert Function not present in $this->service_query_construct Class");
			return false;
		}
	}
	
	public function editAction(){
		console(LOG_LEVEL_TRACE,"Inside editService function.");
		global $request,$db;
		if(method_exists($this->service_query_construct, 'updateQuery')){
			console(LOG_LEVEL_TRACE,"Calling function to Create Update Query.");
			$this->query = $this->service_query_construct->updateQuery($this->bean_obj,$this->where);
			if($this->query){
				console(LOG_LEVEL_TRACE,"Calling function query() to execute update query.");
				$sqlstatus = $db['master']->query($this->query);
				if($sqlstatus){
					console(LOG_LEVEL_TRACE,"Calling function selectQuery() to fetch the last inserted record.");
					$this->query = $this->service_query_construct->selectQuery($this->bean_obj,$this->where,1);
					if($this->query){
						console(LOG_LEVEL_TRACE,"Fetching last row inserted.");
						$resultdata = $db['master']->getOneRow($this->query);
						if($resultdata){
							return $resultdata;
						}else{
							console(LOG_LEVEL_TRACE,"Not able to fetch last row inserted.");
							return false;
						}
					}else{
						console(LOG_LEVEL_TRACE,"Query returned from selectQuery() is blank.");
						return false;
					}
				}else{
					console(LOG_LEVEL_TRACE,"Not able to update a record.");
					return false;
				}
			}else{
				console(LOG_LEVEL_TRACE,"Not able to genrate a update query.");
				return false;
			}
		}else{
			print "Update Function not present in $this->service_query_construct Class";
			console(LOG_LEVEL_TRACE,"Update Function not present in $this->service_query_construct Class");
			return false;
		}
	}
	
	public function deleteAction(){
		console(LOG_LEVEL_TRACE,"Inside Delete Function.");
		global $request,$db;
		if(method_exists($this->service_query_construct, 'deleteQuery')){
			console(LOG_LEVEL_TRACE,"Calling function to Create Delete Query.");
			$this->query = $this->service_query_construct-> deleteQuery($this->bean_obj,$this->where);
			if($this->query){
				console(LOG_LEVEL_TRACE,"Calling function query() to execute delete query.");
				$resultdata = $db['master']->query($this->query);
				if($resultdata){
					console(LOG_LEVEL_TRACE,"Record successfully deleted.");
					print "Successfully Deleted.";
					return true;
				}else{
					console(LOG_LEVEL_TRACE,"Deletion Failed.");
					return false;
				}
			}else{
				console(LOG_LEVEL_TRACE,"Not able to genrate a delete query.");
				return false;
			}
		}else{
			print "Delete Function not present in $this->service_query_construct Class";
			console(LOG_LEVEL_TRACE,"Delete Function not present in $this->service_query_construct Class");
			return false;
		}
	}
	
	public function selectAction(){
		console(LOG_LEVEL_TRACE,"Inside selectService Function.");
		global $request;
		global $db;
		if(method_exists($this->service_query_construct, 'selectQuery')){
			console(LOG_LEVEL_TRACE,"Calling function to Create select Query.");
			$this->query = $this->service_query_construct->selectQuery($this->bean_obj,$this->where,$this->limit);
			if(!$this->query){
					console(LOG_LEVEL_TRACE,"Not able to genrate a select query.");
					return false;
			}
			$result_temp_fetch = array();
			console(LOG_LEVEL_TRACE,"Fetching Number of records.");
			$resultdata = $db['master']->getResults($this->query);
			if($resultdata){
				console(LOG_LEVEL_TRACE,"Records Successfully Fetched.");
				return $resultdata;
			}else{
				console(LOG_LEVEL_TRACE,"Not Able to Fetch Record.");
				return false;
			}
		}else{
			print "Select Function not present in $this->service_query_construct Class";
			console(LOG_LEVEL_TRACE,"Select Function not present in $this->service_query_construct Class.");
			return false;
		}
	}
	
	//Function to create QueryConstruct Object.
	public function createQueryConstructObj(){
		console(LOG_LEVEL_TRACE,"Inside createQueryConstructObj() function.");
		console(LOG_LEVEL_TRACE,"Including file for Query Construction.");
		require_abs ( 'framework/library/utils/queryconstruct.php');
		console(LOG_LEVEL_TRACE,"Creating object for Query Construct class.");
		$this->service_query_construct = new QueryConstruct();
	}
}
?>