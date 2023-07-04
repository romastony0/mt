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
 * @filename:	loadLibrary.class.inc
 * @filetype:	PHP
 * @filedesc:	this file is used to load the library needed for
 * 				for the current functionality.
 *
 */

class LoadLibrary {
	
	private $_libraryInc;
	private $_loadLibrary;
	
	/**
	 * Description:
	 * Constructor for load Library to intialize the  Load Library varibles.
	 *
	 * Parameters:
	 *	null								:	null
	 * Return Value: 
	 *	null								:	null	
	 *	null									:	null
	 */
	function __construct() {
		$this->_libraryInc = array ();
		$this->_loadLibrary = array ();
	}
	
	/**
	 * Description:
	 * function will add the new Library to the framewaork.
	 *
	 * Parameters:
	 *	FileLoader $fileLoaderObj			:	$fileLoaderObj of file loader class.
	 * Return Value: 
	 *	boolean true/false					:	true if the library not present in the array and added successfully.
	 *										:	false if the library already present in the array.
	 */
	function addLibrary($fileLoaderObj) {
		$libName = $fileLoaderObj->getLibraryName ();
		if (! isset($this->_loadLibrary [$libName])) {
			$this->_libraryInc [$libName] = $fileLoaderObj->getFileList ();
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Description:
	 * function will load the respective library that are defined for library	 *
	 * Parameters:
	 *	string library name					:	$libraryName name of the library for which we want to load library.
	 * Return Value: 
	 *	boolean true/false					:	true if library is defined and loaded properly.
	 *										:	false if not able to load library.
	 */
	function loadLibrary($libraryName) {
		if (isset($this->_loadLibrary [$libraryName])) {
			return false;
		} else {
			if($this->checkLibraryInc($libraryName))
			{
				return true;
			}
			$this->_loadLibrary [$libraryName] = $libraryName;
			foreach ( $this->_libraryInc [$libraryName] ['file'] as $key => $value ) {
				if ($this->_libraryInc [$libraryName] ['flag'][$key] === false) {					
					require_once ($value);
				} else {
					require_abs ( $value );
				}
			}
		}
		return true;
	}
	
	function getLibraryInc(){
		return $this->_libraryInc;
	}
	
	function getLoadLibrary(){
		return $this->_loadLibrary;
	}
	
	function checkLibraryInc($libName){
		if(isset($this->_loadLibrary[$libName])){
			return $this->_libraryInc[$libName];
		}else{
			return false;
		}
	}
}
?>