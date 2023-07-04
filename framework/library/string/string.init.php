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
 * @filename:	string.init.php
 * @filetype:	PHP
 * @filedesc:	this file is used to instantiate the string library
 *
 */

global $_lib_strings;

require_abs ( 'framework/library/string/string.class.inc' );
require_abs ( 'framework/library/string/regex.class.inc' );

$_lib_strings = new Strings ();

/*
Email checking.
LOGIC:
- one or more letter, number or dot characters
- @
- one or more letter, number or dot characters
- dot
- Letters repeated between 2 and 4 times

$regexp=new regExpBuilder(CASE_INSENSITIVE);
$regexp->matchLineStart()	//Perform the check starting from the begin of the string
->matchOneOfTheseChars(LETTER_CHAR.DIGIT_CHAR.".")->frequency(ONE_OR_MORE)	//Letter, number or dot repeated on ore more times
->match("@")	//@ sign
->matchOneOfTheseChars(LETTER_CHAR.DIGIT_CHAR.".")->frequency(ONE_OR_MORE)	//Letter, number or dot repeated on ore more times
->match(".")	//dot
->matchOneOfTheseChars(LETTER_CHAR)->frequency(2,4)	//Letters repeated between 2 and 4 times
->matchLineEnd();	//Match the end of the string

echo "example@email.com: ".($regexp->testOn("example@email.com") ? "true" : "false"); //True
echo "<br>example@email.comwrong: ".($regexp->testOn("example@email.comwrong") ? "true" : "false"); //False
*/

?>