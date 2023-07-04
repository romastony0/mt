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
 * @filename:	browser.init.php
 * @filetype:	PHP
 * @filedesc:	this file is used to instantiate the browser library
 *
 */
global $browser;

require_abs ( 'framework/library/browser/browser.class.inc' );

$browser = new BrowserInfo ();
$browser->cssType = ($browser->isIE ()) ? "text/plain" : "text/css";

define ( "USERAGENT", getenv ( "HTTP_USER_AGENT" ) );
define ( "BROWSERNAME", $browser->ShowBrowserInfo () );

/*
 * echo $BROWSEROBJECT->BrowserName." ".$BROWSEROBJECT->BrowserVersion."<BR><BR>";
 * echo "Major: $BROWSEROBJECT->BrowserMajorVersion<BR>"; 
 * echo "Minor: $BROWSEROBJECT->BrowserMinorVersion<BR>";
 * echo "Release: $BROWSEROBJECT->BrowserReleaseVersion<BR>";
 * echo "Build: $BROWSEROBJECT->BrowserBuildVersion<BR>";
 * $BROWSEROBJECT->isIE() to detect ONLY IE;
 * $BROWSEROBJECT->isOpera() to detect ONLY Opera;
 * $BROWSEROBJECT->isMozilla() to detect ONLY Mozilla;
 * $BROWSEROBJECT->isMoz() to detect any Mozilla/Firefox Compatible Browsers;
 * 
 * BrowserInfo::isIE()
 * BrowserInfo::isOpera()
 * BrowserInfo::isMoz()
 * if you use any of these methods the BrowserInfo() function won't be instantated;
*/

?>