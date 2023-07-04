<?php
/**
 * @author Ronald Jacob R
 * @version  1.0
 */

class FileSystemUtilities {
	
	/**
	 * this function is used to check a local file
	 * the function returns false if it fails but returns true when it succeeds
	 *
	 * @param string $file
	 * @return boolean
	 */
	function fileCheck($file) { // file check function begins here
		//condition to check file exists
		if (file_exists ( $file )) {
			//if file exists
			//check if user has read permisssion to the file
			if (is_readable ( $file )) {
				//if user has read permission to the file
				//check if user has write permission to the file
				if (is_writable ( $file )) {
					//if user has write permision to the file return true as error code
					return true;
				} 

				//if write check fails
				else {
					//return error code as false since user cannot write to the file
					return false;
				}
			} 

			//if read check fails
			else {
				//return error code as false since user does not have read permission to the file
				return false;
			}
		} 

		//if file does not exists
		else {
			//return error code as false because the file does not exists
			return false;
		}
		//end of file check function
	}
	
	/**
	 * this function is used to read local files
	 * the function returns false if it fails but returns the file stream when it succeeds
	 *
	 * @param string $file
	 * @return boolean
	 */
	function fileRead($file) { //function file read begins here
		//condition to check if file exists
		if (FileSystemUtilities::fileCheck ( $file ) == true) { //if file exists open it for reading
			if (! $file_handler = fopen ( $file, 'rb' )) {
				//return error code as false as the file cannot be opened for reading
				return false;
			} 

			//if the file can be opened for reading
			else {
				//read the file stream
				if (($file_stream = fread ( $file_handler, filesize ( $file ) )) === FALSE) {
					//if file stream cannot be opened for reading return error code as false
					return false;
				} 

				//if file read succeeds
				else {
					//close the file pointer
					fclose ( $file_handler );
					//return the the file strem read in to the buffer
					return $file_stream;
				}
			}
		} 

		//if file does not exists
		else {
			//return false as the error code because file does not exist
			return false;
		}
		//end of file read function
	}
	
	/**
	 * this function is used to write to local files
	 * the function returns false if it fails but returns true when it succeeds
	 * the mode call can either be false or true
	 * if it is false write uses append method else uses create new method
	 *
	 * @param string $file
	 * @param resource $file_stream
	 * @param mixed_type $mode
	 * @return boolean
	 */
	function fileWrite($file, $file_stream, $mode) {
		//file write function begins here
		//check if file exists
		if (FileSystemUtilities::fileCheck ( $file ) == true) {
			//if file exists then find which method is passed
			//if mode true is passed
			if ($mode == false) {
				//open file in append mode
				if (! $file_handler = fopen ( $file, 'a+b' )) {
					//if not able o open file return false as the error code
					return false;
				} 

				//if the condition succeeds
				else {
					//write to the file
					if (fwrite ( $file_handler, $file_stream ) === FALSE) {
						//if not able to write to the file return false as the error code
						return false;
					} 

					//if write succeeds
					else {
						//close file handler
						fclose ( $file_handler );
						//return error code as true since the function has succeeded
						return true;
					}
				}
			} //end of mode true


			//if the specified mode is false
			elseif ($mode == true) {
				//open file in write mode
				if (! $file_handler = fopen ( $file, 'w+b' )) {
					//if cannot open file return error code as false
					return false;
				} 

				//if open succeeds
				else {
					//write the contents to the file
					if (fwrite ( $file_handler, $file_stream ) === FALSE) {
						//if cannot write contents to the file return false as the error code
						return false;
					} 

					//if write succeeds
					else {
						//close the file handler
						fclose ( $file_handler );
						//return as true as the error code since the function has succeds
						return true;
					}
				
				}
			} 

			//if neither false or true is passed as the argument then throw exception
			else {
				//return invalid argument error with error code false
				return false;
			}
		} 

		//if the file check fails
		else {
			//return the file has failed with error code as false
			return false;
		}
		//end of file write function
	}
	
	/**
	 * this function is used to copy local files
	 * the function returns false if it fails but returns true when it succeeds
	 *
	 * @param string $file_old_location
	 * @param string $file_new_location
	 * @return boolean
	 */
	function fileCopy($file_old_location, $file_new_location) {
		//file copy function begins here
		//condition to check if copy function has succeeded
		if (! copy ( $file_old_location, $file_new_location )) {
			//if the file copy function fails return false as the error code
			return false;
		} 

		//if the condition succeeds
		else {
			//return the error code as true if the function succeeds
			return true;
		}
		//end of file copy function
	}
	
	/**
	 * this function is used to rename local files
	 * the function returns false if it fails but returns true when it succeeds
	 *
	 * @param string $file_old_name
	 * @param string $file_new_name
	 * @return boolean
	 */
	function fileRename($file_old_name, $file_new_name) { //rename function begins here
		//condition to check if rename is successfull
		if (rename ( $file_old_name, $file_new_name ) === FALSE) {
			//if the rename funtion fails return false as error code
			return false;
		} 

		//if the condition succeeds
		else {
			//return the error code as true to show that the function has succeeded
			return true;
		}
		//end of rename function
	}
	
	/**
	 * this function is used to delete local files
	 * the function returns false if it fails but returns true when it succeds
	 *
	 * @param string $file
	 * @return unknown
	 */
	function fileDelete($file) { //file delete function begins here
		//condition to check if the delete function is successfull
		if (@unlink ( $file ) === FALSE) {
			//if the file delete function fails return false as the error code
			return false;
		} 

		//if the condition succeeds
		else {
			//return error code as true to show that the function has succeeded
			return true;
		}
		// end of file delete function
	}
	
	/**
	 * this function is used to create a local directory
	 * the function returns false if it fails but returns true when it succeds
	 *
	 * @param string $directory
	 * @return boolean
	 */
	function directoryCreate($directory) { //function create dir begins here
		if (@mkdir ( $directory, 0777, true ) === FALSE) {
			//if the dir cannot be created return false as the error code
			return false;
		} 

		// if the function succeeds
		else {
			//return error code as true if the function succeeds
			return true;
		}
		//end of create dir function
	}
	
	/**
	 * this function is used to delete a local directory
	 * the function returns false if it fails but returns true when it succeds
	 *
	 * @param string $directory
	 * @return boolean
	 */
	function directoryDelete($directory) { //function delete dir begins here
		if (@rmdir ( $directory ) === FALSE) {
			//if the dir cannot be deleted return false as the error code
			//echo "The Directory $directory cannot be deleted <br />\n";
			return false;
		} 

		// if the function succeeds
		else {
			//return error code as true if the function succeeds
			return true;
		}
		//end of delete dir function
	}

}

?>