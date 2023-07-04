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
 * @filename:	queryvalidator.php
 * @filetype:	PHP
 * @filedesc:   This file is used for validating Database Query to insert,update,delete and select.
 * 
 *
 */

class QueryValidator{
	
	private $errors = array();
	private $validation_rules = array();
	private $source = array();
	private $default_msg = 'Invalid data';
	private static $valid_obj;
	/**
	 * constructor 
	 */
	public function __construct(){}
	/**
	 * get QueryValidator object instance
	 * return object
	 */
	public function getValidatorinstance(){
		
		if(!is_object(self::$valid_obj)) {  
             self::$valid_obj = new self();  
	    }  
        return self::$valid_obj;
	}
	/**
	 * add validations rules
	 * @param $rules_array form field validation rules
	 */
 	public function addRules(array $rules_array){
    
        $this->validation_rules = array_merge($this->validation_rules, $rules_array);
    }
    
    /**
     * get list of validation rules
     */
    
	public function getRules(){
    
        return $this->validation_rules;
    }
    
    public function resetRules(){
    	$this->validation_rules = array();
    }
    
    public function resetSource(){
    	$this->errors = array();
    	$this->source = array();
    }
    
    /**
     * Add form data to object
     * @param $field
     */
    public function addSource(array $field){

    	$this->source = $field;
    }
    
    /**
     * get final formated and validated form data
     */
    public function getSource(){
    	
    	return $this->source; 
    }
    
    /**
     * check form field is empty
     * @param $field form field name
     * @param $msg error msg
     */
    /*private function is_set($field,$msg){
    	
    	if(!isset($this->source[$field])){
    		//$this->errors[$field] = $msg;
    		$this->errors[$field] = "value isn't set";
    	}
    }*/
    
    /**
     * form validation contain errors
     */
    public function isError(){
    	
    	return (count($this->errors)>0)? true : false;
    }
    /**
     * get list of form validation error messages
     */
    public function getError(){
    	
    	return $this->errors;
    }
    /**
     * reset form validation error array
     */
    public function resetError(){
    	
    	$this->errors = array();
    }
    /**
     * validate form field data
     */
    public function validate(){

    	foreach($this->validation_rules as $field=>$values){
    		
    		$msg = isset($values['message'])?$values['message']:$this->default_msg;
			$max = isset($values['length'])?$values['length']:0;

    		/*** validate field value is set ***/
            if(array_key_exists('required', $values)){
            	
            	if(!$values['required'] and !isset($this->source[$field])){
            		continue;
            	}else{
            		if(!isset($this->source[$field])){
    					$this->errors[$field] = "value isn't set";
    					continue;
    				}
            	}
            }
            
    		/*** Trim whitespace from beginning and end of variable ***/
            if(array_key_exists('trim', $values) && $values['trim'] == true ){
            
                $this->source[$field] = trim($this->source[$field]);
            }
            
    		/*** strip html tags if exists ***/
            if(array_key_exists('striptags', $values) && $values['striptags'] == true ){
            
                $this->source[$field] = strip_tags($this->source[$field]);
            }
            
            if(isset($values['type'])){
            	$values['type'] = strtolower($values['type']);
            	
            	switch($values['type']){
            		
                case 'email':
                    $this->isEmail($field,$msg,$max);
                    break;

                case 'url':
                    $this->isUrl($field,$msg,$max);
                    break;

                case 'numeric':
                    $this->isNumeric($field,$msg,$max);
                    break;

                case 'string':
                    $this->isString($field,$msg,$max);
                    break;

                case 'float':
                    $this->isFloat($field,$msg,$max);
                    break;

                case 'ipv4':
                    $this->isIpv4($field,$msg,$max);
                    break;

                case 'ipv6':
                    $this->isIpv6($field,$msg,$max);
                    break;

                case 'bool':
                    $this->isBool($field,$msg,$max);
                    break;
                    
                case 'phone':
                    $this->isPhone($field,$msg,$max);
                    break;
                
                case 'zip':
                    $this->isZip($field,$msg,$max);
                    break;
                    
                case 'date':
                	$this->isDate($field,$msg,$max);
                    break;
                    
                case 'time':
                    $this->isTime($field,$msg,$max);
                    break;
                    
                case 'timestamp':
                    $this->isTimestamp($field,$msg,$max);
                    break;
                    
                case 'range':
                	$min = isset($values['min'])?$values['min']:0;
                	$max = isset($values['max'])?$values['max']:0;
                    $this->isRange($field,$msg,$min,$max);
                	break;
                	
                case 'regexp':
                	$pattern = isset($values['pattern'])?$values['pattern']:null;
                    $this->isRegexp($field,$msg,$max,$pattern);
                    break;
                case 'enum':
                	if(!in_array($this->source[$field], $values['values'])){
                		$this->errors[$field] = $msg." for ".$field ;
                	}
                	break;
                case "array":
                	if(is_array($this->source[$field])){
                		if(!$this->source[$field]){
                			$this->errors[$field] = $msg." for ".$field ;	
                		}
                	}else{
                		$this->errors[$field] = $msg." for ".$field ;
                	}
                	break;
                default:
                	$this->errors[$field] = $values['type'].' validation type is undefined for field '.$field;
                	break;
                
            	}
            }
            
    		/*** htmlspecialchars encode ***/
            if(array_key_exists('htmlspecialchars', $values) && $values['htmlspecialchars'] == true ){
            
                $this->source[$field] = htmlspecialchars($this->source[$field]);
            }
            
    		/*** htmlentities encode ***/
            if(array_key_exists('htmlentities', $values) && $values['htmlentities'] == true ){
            
                $this->source[$field] = htmlentities($this->source[$field]);
            }
        }
    }
    /**
     * check form data exceds min and max limit
     * @param unknown_type $field form field name
     * @param unknown_type $max form field max value
     */
    private function isMinMax($field,$max=0){
    	
    	if($max and (strlen($this->source[$field]) > $max)){
        	
            $this->errors[$field] = $field . ' is too long';
            return true;
        }else{ return false;}
    }
    /**
     * check form data is valid ipv4 address
     * @param unknown_type $field form field name
     * @param unknown_type $max form field max value
     */
	private function isIpv4($field,$msg,$max=0)
    {
    	$limitstatus = $this->isMinMax($field,$max);
        if(!$limitstatus and filter_var($this->source[$field], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) === FALSE){
        
            $this->errors[$field] = $msg;
        }
       
    }
	/**
     * check form data is valid ipv6 address
     * @param unknown_type $field form field name
     * @param unknown_type $max form field max value
     */
    private function isIpv6($field,$msg,$max=0)
    {
        $limitstatus = $this->isMinMax($field,$max);
        if(!$limitstatus and filter_var($this->source[$field], FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) === FALSE){
        
            $this->errors[$field] = $msg;
        }
    }
	/**
     * check form data is float type
     * @param unknown_type $field form field name
     * @param unknown_type $max form field max value
     */
    private function isFloat($field,$msg,$max=0)
    {
        $limitstatus = $this->isMinMax($field,$max);
        if(!$limitstatus and filter_var($this->source[$field], FILTER_VALIDATE_FLOAT) === false){
        	
            $this->errors[$field] = $msg;
        }
    }
	/**
     * check form data is int type
     * @param unknown_type $field form field name
     * @param unknown_type $max form field max value
     */
    private function isString($field,$msg,$max=0)
    {
        $limitstatus = $this->isMinMax($field,$max);
        if(!$limitstatus and !is_string($this->source[$field])){
        	
            $this->errors[$field] = $msg;
        }
        
    }
	/**
     * check form data is string type
     * @param unknown_type $field form field name
     * @param unknown_type $max form field max value
     */
    private function isNumeric($field,$msg,$max=0){
        
        $limitstatus = $this->isMinMax($field,$max);
        if(!$limitstatus and filter_var($this->source[$field], FILTER_VALIDATE_INT)===FALSE){
        
            $this->errors[$field] = $msg;
        }
    }
	/**
     * check form data is url type
     * @param unknown_type $field form field name
     * @param unknown_type $max form field max value
     */
    private function isUrl($field,$msg,$max=0){
        
        $limitstatus = $this->isMinMax($field,$max);
        if(!$limitstatus and filter_var($this->source[$field], FILTER_VALIDATE_URL) === FALSE){
        
            $this->errors[$field] = $msg;
        }
    }
	/**
     * check form data is email type
     * @param unknown_type $field form field name
     * @param unknown_type $max form field max value
     */
    private function isEmail($field,$msg,$max=0){
        
        $limitstatus = $this->isMinMax($field,$max);
        if(!$limitstatus and filter_var($this->source[$field], FILTER_VALIDATE_EMAIL) === FALSE){
        	
            $this->errors[$field] = $msg;
        }
    }
	/**
     * check form data is boolean type
     * @param unknown_type $field form field name
     * @param unknown_type $max form field max value
     */
    private function isBool($field,$msg,$max=0){
        
        $limitstatus = $this->isMinMax($field,$max);
        if(!$limitstatus and filter_var($this->source[$field], FILTER_VALIDATE_BOOLEAN) === FALSE){
        	
            $this->errors[$field] = $msg;
        }
    }
	/**
     * check form data is phone number type
     * @param unknown_type $field form field name
     * @param unknown_type $max form field max value
     */
    private function isPhone($field,$msg,$max=0){
        
        $limitstatus = $this->isMinMax($field,$max);
        if(!$limitstatus and filter_var($this->source[$field], FILTER_VALIDATE_REGEXP, array("options" => array("regexp"=>'/^[0-9]{10,12}$/')))===FALSE){
        	
            $this->errors[$field] = $msg;
        }
    }
	/**
     * check form data is zipcode type
     * @param unknown_type $field form field name
     * @param unknown_type $max form field max value
     */
    private function isZip($field,$msg,$max=0){
        
        $limitstatus = $this->isMinMax($field,$max);
        if(!$limitstatus and filter_var($this->source[$field], FILTER_VALIDATE_REGEXP, array("options" => array("regexp"=>'/^[0-9]{5,6}$/')))===FALSE){
        	
            $this->errors[$field] = $msg;
        }
    }
	/**
     * check form data is date type
     * @param unknown_type $field form field name
     * @param unknown_type $max form field max value
     */
    private function isDate($field,$msg,$max=0){
        
        $limitstatus = $this->isMinMax($field,$max);
    	if(!$limitstatus and filter_var($this->source[$field], FILTER_VALIDATE_REGEXP, array("options" => array("regexp"=>'/^[0-9]{4}(-[0-9]{2}){2}$/')))===FALSE){
        	
            $this->errors[$field] = $msg;
        }
    }
	/**
     * check form data is time type
     * @param unknown_type $field form field name
     * @param unknown_type $max form field max value
     */
    private function isTime($field,$msg,$max=0){
        
    	$limitstatus = $this->isMinMax($field,$max);
    	if(!$limitstatus and filter_var($this->source[$field], FILTER_VALIDATE_REGEXP, array("options" => array("regexp"=>'/^[0-9]{2}(:[0-9]{2}){2}$/')))===FALSE){
        	
            $this->errors[$field] = $msg;
        }
    }
	/**
     * check form data is timestamp type
     * @param unknown_type $field form field name
     * @param unknown_type $max form field max value
     */
    private function isTimestamp($field,$msg,$max=0){
        
    	$limitstatus = $this->isMinMax($field,$max);
    	if(!$limitstatus and filter_var($this->source[$field], FILTER_VALIDATE_REGEXP, array("options" => array("regexp"=>'/^[0-9]{4}(-[0-9]{2}){2} [0-9]{2}(:[0-9]{2}){2}$/')))===FALSE){
        	
            $this->errors[$field] = $msg;
        }
    }
	/**
     * check form data is min and max limit 
     * @param unknown_type $field form field name
     * @param unknown_type $min form field min value
     * @param unknown_type $max form field max value
     */
    private function isRange($field,$msg,$min=0,$max=0){
    	
    	if(($min and $this->source[$field] < $min) or ($max and $this->source[$field] > $max)){
        	
            $this->errors[$field] = $msg;
        }
    }
    /**
     * check form data matches regular expression
     * @param unknown_type $field form field name
     * @param unknown_type $min form field min value
     * @param unknown_type $max form field max value
     */
	private function isRegexp($field,$msg,$max=0,$pattern){
        
        $limitstatus = $this->isMinMax($field,$max);
        if(!$limitstatus and filter_var($this->source[$field], FILTER_VALIDATE_REGEXP, array("options" => array("regexp"=>$pattern)))===FALSE){
        
            $this->errors[$field] = $msg;
        }
    }
    
}