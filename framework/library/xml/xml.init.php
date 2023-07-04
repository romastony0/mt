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
 * @filename:	xml.init.php
 * @filetype:	PHP
 * @filedesc:	this file is used to instantiate the xml library
 *
 */

global $libxml;

class XMLInit {
	//xml string/file
	public $xml;
	//array object
	public $array;
	//arraytoxml object
	private $arraytoxml;
	//xmltoarray object
	private $xmltoarray;
	//if xml is available pass it on init
	function __construct($xml = null, $array = null) {
		$this->xml = $xml;
		$this->array = $array;
	}
	
	/**
	 * 
	 * function to get the xmltoarray object
	 * @param string $xml
	 */
	public function getXMLToArrayObj($xml = null) {
		if ($xml) {
			$this->flush ();
			$this->xml = $xml;
		}
		$this->xmltoarray = new XMLtoArray ( $this->xml );
		return $this->xmltoarray;
	}
	
	/**
	 * 
	 * funtion to directly get the Array from the passed XML
	 * @param string $xml
	 */
	public function getArray($xml = null) {
		if ($xml)
			$this->xml = $xml;
		$this->xmltoarray = new XMLtoArray ( $xml );
		$array = $this->xmltoarray->getResult ();
		$this->flush ();
		return $array;
	}
	
	/**
	 * 
	 * function to get the arraytoxml object
	 * @param array $array
	 */
	public function getArrayToXMLObj($array = null) {
		if ($array) {
			$this->flush ();
			$this->array = $array;
		}
		$this->arraytoxml = new ArraytoXML ( $this->array );
		return $this->arraytoxml;
	}
	
	/**
	 * 
	 * funtion to directly get the XML from the passed Array
	 * @param array $array
	 */
	public function getXML($array = null) {
		$this->array = $array;
		$this->arraytoxml = new ArraytoXML ( $this->array );
		$xml = $this->arraytoxml->getResult ();
		$this->flush ();
		return $xml;
	}
	
	/**
	 * 
	 * function to clear out the xml/array data/objects
	 */
	public function flush() {
		unset ( $this->xml );
		unset ( $this->array );
		unset ( $this->arraytoxml );
		unset ( $this->xmltoarray );
	}
}

$libxml = new XMLInit ();

?>