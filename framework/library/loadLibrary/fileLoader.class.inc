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
 * @filename:	fileLibrary.class.inc
 * @filetype:	PHP
 * @filedesc:	this file is used to create a fileloader  object
 * 				to load files which are needed by the module. 
 * 
 */

class FileLoader {
	private $_fileList;
	private $_libraryName;
	
	/**
	 * Description:
	 * constructor to intialize the private members for FileLoader class.
	 *
	 * Parameters:
	 *	null								:	null
	 * Return Value: 
	 *	null								:	null
	 */
	function __construct($libraryName) {
		$this->_libraryName = $libraryName;
		$this->_fileList = array ();
	}
	
	/**
	 * Description:
	 * function will add file to a file Loader object.
	 *
	 * Parameters:
	 *	null								:	null
	 * Return Value: 
	 *	null								:	null
	 */
	function addFile($file, $flag = true) {
		$this->_fileList ['file'] [] = $file;
		$this->_fileList ['flag'] [] = $flag;
	}
	
	/**
	 * Description:
	 * function is a getter function for accessing private member varible Libraryname.
	 *
	 * Parameters:
	 *	null								:	null
	 * Return Value: 
	 *	null								:	null
	 */
	function getLibraryName() {
		return $this->_libraryName;
	}
	
	/**
	 * Description:
	 * function is a getter function for accessing private member varible Filelist associate with current object.
	 *
	 * Parameters:
	 *	null								:	null
	 * Return Value: 
	 *	null								:	null
	 */
	function getFileList() {
		return $this->_fileList;
	}
}
?>