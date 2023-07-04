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
 * @filename:	browser.class.inc
 * @filetype:	PHP
 * @filedesc:	this file is used to find the user browser types
 *
 */

/*
 * Mozilla Definition - Gecko-based applications
 *   Anything Based on gecko Runtime, eg:
 *    Mozilla Suite 0.2 - 1.7.13, later Seamonkey (Suite) 1.0 - 1.1   (1)
 *	 Firefox 1.0+ (previously Firebird & Phoenix)   (1)
 *	 Netscape 6,7,8,9 (8 & 9 are based more off Firefox,6 & 7 ar based more off the Mozilla Suite) (1)
 *	 Flock   (1)
 *	 K-meleon (Windows)
 *	   [MAC]
 *	  Chimera
 *	  Camino
 *	  Safari (Firefox/Konqueror Clone) - KHTML
 *	   [Linux/Unix]
 *	  Epiphany
 *	  Galeon
 *	  Konqueror -- Note, Konqueror also has what they call KHTML.
 *	    KHTML Definition: en.wikipedia.org/wiki/KHTML (1/1/2007)
 *	  
 *  - you get the idea there's alot!
 * this is known as the Mozilla/Gecko Core.
 *
 *   (1) Works on Windows,Linux,UNIX,Macintosh ....
 *   
 *   also see:
 *    http://en.wikipedia.org/wiki/Gecko_%28layout_engine%29#Web_browsers
 *    for Gecko-based applications
 *
 */


class BrowserInfo {
	public $BrowserName = "";
	public $BrowserNick = "";
	public $BrowserVersion = "";
	public $BrowserMajorVersion = "";
	public $BrowserMinorVersion = "";
	public $BrowserReleaseVersion = "";
	public $BrowserBuildVersion = "";
	public $BROWSERNAME = "";
	public $USERAGENT = "";
	public $cssType = "";
	public $inValidBrowser = "";
	public $IEVER = array ();
	public $MOZVER = array ();
	public $USEROS = "";
	public $BrowserVer = array ('Major' => 0, 'Minor' => 0, 'Release' => 0, 'Build' => 0 );
	function __construct() {
		$this->USERAGENT = getenv ( "HTTP_USER_AGENT" );
		$this->BrowserName = "Unknown";
		$this->USEROS = "Unknown";
		if (preg_match ( "/win/i", $this->USERAGENT ))
			$this->USEROS = "Windows";
		if (preg_match ( "/mac/i", $this->USERAGENT ))
			$this->USEROS = "Macintosh";
		if (preg_match ( "/unix/i", $this->USERAGENT ))
			$this->USEROS = "UNIX";
		if (preg_match ( "/linux/i", $this->USERAGENT ))
			$this->USEROS = "Linux";
		if (preg_match ( "/beos/i", $this->USERAGENT ))
			$this->USEROS = "BeOS"; # Dead

		# MSIE
		if (preg_match ( "/msie/i", $this->USERAGENT ) && ! $this->isOpera ()) {
			$this->BrowserName = "Microsoft Internet Explorer"; #Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)
			$this->BrowserNick = "msie";
			$this->BrowserVersion = preg_replace ( "/(.*)(msie\s)([0-9]+\.[0-9]+)(;)(.*)/i", "$3", $this->USERAGENT );
		}
		# OPERA
		if (preg_match ( "/opera/i", $this->USERAGENT )) {
			$this->BrowserName = "Opera";
			$this->BrowserNick = "opera";
			$this->BrowserVersion = preg_replace ( "/(.*)(Opera[\s|\/])([0-9]+\.[0-9]+)(.*)/i", "$3", $this->USERAGENT );
		}
		#Mozilla/Seamonkey Suites
		if (preg_match ( "/(rv:)|(seamonkey)/i", $this->USERAGENT ) && ! preg_match ( "/(netscape)|(navigator)/i", $this->USERAGENT )) {
			$this->BrowserName = (preg_match ( "/seamonkey/i", $this->USERAGENT )) ? "Seamonkey" : "Mozilla";
			$srch = (preg_match ( "/seamonkey/i", $this->USERAGENT )) ? "(seamonkey\/)" : "(rv:)([0-9\.]+)";
			$this->BrowserVersion = preg_replace ( "/(.*)" . $srch . "(.*)/i", "$3", $this->USERAGENT );
			$this->BrowserNick = (preg_match ( "/seamonkey/i", $this->USERAGENT )) ? "seamonkey" : "mozilla";
		}
		## Firefox/FireBird/Phoenix/GranParadiso
		if (preg_match ( "/(firefox)|(firebird)|(phoenix)|(GranParadiso)/i", $this->USERAGENT )) {
			$this->BrowserName = "Phoenix";
			if (preg_match ( "/firebird/i", $this->USERAGENT ))
				$this->BrowserName = "Firebird";
			if (preg_match ( "/firefox/i", $this->USERAGENT ))
				$this->BrowserName = "Firefox";
			if (preg_match ( "/GranParadiso/i", $this->USERAGENT ))
				$this->BrowserName = "GranParadiso"; #ff3
			$this->BrowserVersion = preg_replace ( "/(.*)(" . $this->BrowserName . "\/)(.*)/i", "$3", $this->USERAGENT );
			$this->BrowserNick = strtolower ( $this->BrowserName );
		}
		# Flock -- Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.0.12) Gecko/20070531 Firefox/1.5.0.12 Flock/0.7.14
		if (preg_match ( "/flock/i", $this->USERAGENT )) {
			$this->BrowserName = "Flock";
			$this->BrowserNick = strtolower ( $this->BrowserName );
			$this->BrowserVersion = preg_replace ( "/(.*)(" . $this->BrowserName . "\/)(.*)/i", "$3", $this->USERAGENT );
		}
		# NS 4, 3...
		if (preg_match ( "/mozilla\/4/i", $this->USERAGENT ) && $this->BrowserName == "Unknown") {
			$this->BrowserName = "Netscape";
			$this->BrowserNick = "ns4";
			$this->BrowserVersion = preg_replace ( "/(Mozilla\/)([0-9\.]+)(.*)/", "$2", $this->USERAGENT );
		}
		# NS 6+
		if (preg_match ( "/(netscape)|(navigator)/i", $this->USERAGENT )) {
			$this->BrowserName = "Netscape";
			if (preg_match ( "/navigator/i", $this->USERAGENT ))
				$this->BrowserName .= " Navigator";
			$this->BrowserVersion = preg_replace ( "/(.*)(.*\/)([0-9\.]+)/", "$3", $this->USERAGENT );
			$this->BrowserNick = strtolower ( $this->BrowserName );
		}
		# Epiphany
		if (preg_match ( "/epiphany/i", $this->USERAGENT )) {
			$this->BrowserName = "Epiphany";
			$this->BrowserVersion = preg_replace ( "/(.*)(" . $this->BrowserName . "[\s|\/])([0-9\.]+)/i", "$3", $this->USERAGENT );
			$this->BrowserNick = strtolower ( $this->BrowserName );
		}
		# Galeon
		if (preg_match ( "/galeon/i", $this->USERAGENT )) {
			$this->BrowserName = "Galeon";
			$this->BrowserVersion = preg_replace ( "/(.*)(" . $this->BrowserName . "[\s|\/])([0-9\.]+)/i", "$3", $this->USERAGENT );
			$this->BrowserNick = strtolower ( $this->BrowserName );
		}
		# Konqueror
		if (preg_match ( "/Konqueror/i", $this->USERAGENT )) {
			$this->BrowserName = "Konqueror";
			$this->BrowserVersion = preg_replace ( "/(.*)(" . $this->BrowserName . "[\s|\/]([0-9\.]+))(.*)/i", "$3", $this->USERAGENT );
			$this->BrowserNick = strtolower ( $this->BrowserName );
		}
		# AOL - complex & ugly
		if (preg_match ( "/(AOL)|(America Online)|(America)/i", $this->USERAGENT )) {
			$srch = (preg_match ( "/aol/i", $this->USERAGENT )) ? "aol" : "america online browser";
			#$p = strpos(strtolower($this->USERAGENT),$srch);
			#$tmpStr = substr($this->USERAGENT,$p,strlen($this->USERAGENT)); #AOL 9.0; Windows NT 5.1; InfoPath.1)
			#$tmpStr = preg_replace("/(aol [0-9\.]+)(.*)/i","$1",$tmpStr);
			$this->BrowserName = "America On Line (AOL)";
			# /(.*)(aol )([\d\.\d]+)(.*)/i; 
			$this->BrowserVersion = preg_replace ( "/(.*)(aol )([\d\.\d]+)(.*)/i", "$3", $this->USERAGENT );
			$this->BrowserNick = "aol";
		}
		# Safari
		if (preg_match ( "/Safari/i", $this->USERAGENT )) {
			$this->BrowserName = "Safari";
			#$this->BrowserVersion = preg_replace("/(.*)(safari)([0-9\.]+)/i","$2",$this->USERAGENT);
			if (preg_match ( "/412/", $this->USERAGENT ))
				$this->BrowserVersion = "2.0";
			if (preg_match ( "/419/", $this->USERAGENT ))
				$this->BrowserVersion = "2.0.4";
			if (preg_match ( "/521\.24/", $this->USERAGENT ))
				$this->BrowserVersion = "3.0";
			$this->BrowserNick = strtolower ( $this->BrowserName );
		}
		# Lynx
		if (preg_match ( "/Lynx/i", $this->USERAGENT )) {
			$this->BrowserName = "Lynx";
			$this->BrowserVersion = preg_replace ( "/(lynx\/)([0-9a-z\.]+)(.*)/i", "$2", $this->USERAGENT );
			$this->BrowserNick = strtolower ( $this->BrowserName );
		}
		# Camino/Chimera
		if (preg_match ( "/(Camino)|(Chimera)/i", $this->USERAGENT )) {
			$this->BrowserName = (preg_match ( "/Camino/i", $this->USERAGENT )) ? "Camino" : "Chimera";
			$this->BrowserVersion = preg_replace ( "/(.*)(" . $this->BrowserName . "[\s\/])([0-9\.]+)/i", "$3", $this->USERAGENT );
			$this->BrowserNick = strtolower ( $this->BrowserName );
		}
		#K-Meleon
		if (preg_match ( "/(kmeleon)|(k-meleon)/i", $this->USERAGENT )) {
			$this->BrowserName = "K-Meleon";
			$this->BrowserVersion = preg_replace ( "/(.*)(K-Meleon\/)(.*)/i", "$3", $this->USERAGENT );
			$this->BrowserNick = strtolower ( $this->BrowserName );
		}
		
		#$this->BrowserVersion = preg_replace("//","",$this->USERAGENT);
		$tVer = preg_split ( "/\./", $this->BrowserVersion );
		$this->BrowserMajorVersion = $tVer [0];
		$this->BrowserMinorVersion = $tVer [1];
		$this->BrowserReleaseVersion = $tVer [2];
		$this->BrowserBuildVersion = $tVer [3];
		$this->BrowserVer ['Major'] = $tVer [0];
		$this->BrowserVer ['Minor'] = $tVer [1];
		$this->BrowserVer ['Release'] = $tVer [2];
		$this->BrowserVer ['Build'] = $tVer [3];
		
		for($i = 1; $i <= 10; $i ++)
			$this->IEVER [$i] = ($this->BrowserVersion >= $i && $this->isIE ( ">=" . $i )) ? 1 : 0;
		for($i = 1; $i <= 10; $i ++)
			$this->MOZVER [$i] = ($this->BrowserVersion >= $i && $this->isMoz ( ">=" . $i )) ? 1 : 0;
		$this->BROWSERNAME = $this->BrowserName . " " . $this->BrowserVersion;
		$this->inValidBrowser = ($this->isKonqueror () || $this->isSafari () || $this->isNS4 () || $this->BrowserName == "Unknown" || $this->BrowserName == "Lynx") ? 1 : 0;
	} 
	   
	function isIE($iVal = 0) {
		return (preg_match ( "/MSIE/i", $this->USERAGENT ) && ! preg_match ( "/(opera)/i", $this->USERAGENT ) && $this->is_Ver ( $iVal )) ? 1 : 0;
	}
	
	function isOpera($iVal = 0) { ## All Versions of Opera
		return (preg_match ( "/(opera)/i", $this->USERAGENT ) && $this->is_Ver ( $iVal )) ? 1 : 0;
	}
	
	function isMozilla($iVal = 0) { # All versions Of Mozilla Suite
		return (preg_match ( "/gecko/i", $this->USERAGENT ) && preg_match ( "/rv:/i", $this->USERAGENT ) && ! preg_match ( "/(netscape)|(navigator)|(phoenix)|(firebird)|(firefox)|(kmeleon)|(k-meleon)|(camino)|(chimera)|(konqueror)|(galeon)|(epiphany)|(seamonkey)/i", $this->USERAGENT ) && $this->is_Ver ( $iVal )) ? 1 : 0; #
	} # end Mozilla   
	

	function isFF($iVal = 0) {
		return (preg_match ( "/(phoenix)|(firebird)|(firefox)/i", $this->USERAGENT ) && $this->is_Ver ( $iVal )) ? 1 : 0;
	}
	
	function isMoz($iVal = 0) { # any mozilla 5.
		return (preg_match ( "/mozilla\/5/i", $this->USERAGENT ) && ! preg_match ( "/(opera)/i", $this->USERAGENT ) && $this->is_Ver ( $iVal )) ? 1 : 0;
	}
	
	function isNS4() { # only NS4
		return (preg_match ( "/Mozilla\/4.[0-9]+/i", $this->USERAGENT ) && ! preg_match ( "/(msie)|(opera)/i", $this->USERAGENT )) ? 1 : 0;
	}
	
	function isSafari($iVal = 0) { #Mozilla/5.0 safari/  (Mac) Gecko/
		return (preg_match ( "/(safari)/i", $this->USERAGENT ) && $this->is_Ver ( $iVal )) ? 1 : 0;
	}
	
	function isCamino($iVal = 0) { #Mozilla/5.0 Camino/  (Mac) Gecko/
		return (preg_match ( "/(camino)|(chimera)/i", $this->USERAGENT ) && $this->is_Ver ( $iVal )) ? 1 : 0;
	}
	
	function isKonqueror($iVal = 0) { 
		//Mozilla/5.0 (compatible; Konqueror/2.0.1; X11); Supports MD5-Digest; 
		//Supports gzip encoding, Mozilla/5.0 (compatible; Konqueror/2.2.1; Linux)
		return (preg_match ( "/konqueror/i", $this->USERAGENT ) && $this->is_Ver ( $iVal )) ? 1 : 0;
	} # end Konq
	

	function isGecko() {
		return (preg_match ( "/(gecko)|(konqueror)/i", $this->USERAGENT )) ? 1 : 0;
	} # gecko
	
	function geckoVer() {
		return preg_replace ( "/(.*)(rv:)([0-9\.]+)(.*)/", "$3", $this->USERAGENT );
	}
	
	function ShowBrowserInfo() {
		return $this->BrowserName . " " . $this->BrowserVersion;
	}
	
	function isDom() {
		return ($this->isGecko () || $this->isIE ( '>=5' ) || $this->isOpera ( '>=7' )) ? 1 : 0;
	}
	
	function isBot() {
		return ($this->BrowserName == "Unknown") ? 1 : 0; # Unknown browser is defind as a bot.
	}
	
	function isVer($iVal) { ## >= version passed, eg $BROWSEROBJECT->isVer(5) will check for 5+
		$v = ($iVal) ? $iVal : 0;
		return (floatval ( $this->BrowserVersion ) >= $v) ? 1 : 0;
	}
	
	function is_Ver($iVal = 0) { # now you can use  $BROWSEROBJECT->is_Ver('>=3); $BROWSEROBJECT->is_Ver('<=3'); ...
		$iVal = preg_replace ( "/\s/", "", $iVal );
		$funct = preg_replace ( "/[^\<\>=!]/", "", $iVal );
		$v = preg_replace ( "/[^0-9\.]/", "", $iVal );
		if ($iVal > 0) {
			switch (strtolower ( $funct )) {
				case "<" :
					return (floatval ( $this->BrowserVersion ) < $v) ? 1 : 0;
					break;
				case ">" :
					return (floatval ( $this->BrowserVersion ) > $v) ? 1 : 0;
					break;
				case "<=" :
					return (floatval ( $this->BrowserVersion ) <= $v) ? 1 : 0;
					break;
				case ">=" :
					return (floatval ( $this->BrowserVersion ) >= $v) ? 1 : 0;
					break;
				case "!=" :
				case "<>" :
				case "><" :
					return (floatval ( $this->BrowserVersion ) != $v) ? 1 : 0;
					break;
				default : # equal by default
					return (floatval ( $this->BrowserVersion ) == $v) ? 1 : 0;
					break;
			} # end case/switch
		} #end if $iVal
		return (floatval ( $this->BrowserVersion ) >= $v) ? 1 : 0;
	}
	
	function checkVer($iVal) { #so you can check the WHOLE version
		$tVer = preg_split ( "/\./", $iVal ); #0,1,2,3 eg ff 1.5.0.2 or 1.5.0.7
		#usually has 1.0 version - not much beyond that
		$release = 1; # release always passes
		$build = 1; # build always passes 
		$major = (floatval ( $this->BrowserMajorVersion ) >= $tVer [0]) ? 1 : 0;
		$minor = (floatval ( $this->BrowserMinorVersion ) >= $tVer [1]) ? 1 : 0;
		# now, only pass release & build if defined.
		if ($tVer [2])
			$release = (floatval ( $this->BrowserReleaseVersion ) >= $tVer [2]) ? 1 : 0;
		if ($tVer [3])
			$build = (floatval ( $this->BrowserBuildVersion ) >= $tVer [3]) ? 1 : 0;
		return ($major && $minor && $release && $build) ? 1 : 0;
	}
	
	function isValidBrowser() {
		return ($this->BrowserName && $this->BrowserName != "Unknown") ? 1 : 0;
	}
	
	function opacity($iVal) { # iVal's in Percntages (50(%))  - better way of remembering & doing opacitiy's
		$opa = "";
		$iVal = intval ( preg_replace ( "/[^0-9]/", "", $iVal ) );
		$o = $iVal / 100;
		if ($o == 1)
			$o .= ".0";
		else
			$o .= "0";
		
		if ($this->isDOM ()) {
			if ($this->isIE ())
				$opa = "filter:alpha(opacity=" . intval ( $iVal ) . ");";
			else if ($this->isKonqueror () || $this->isSafari ())
				$opa = "-khtml-opacity:" . $o . ";";
			else if ($this->isMozilla () && floatval ( $this->BrowserVersion ) < 1.7)
				$opa = "moz-opacity:" . $o . ";";
			else
				$opa = "opacity:" . $o . ";";
		}
		return $opa;
	}	

	function transparentpng($png) { # A better way of remembering ie < 7 quirks for transparent pngs.
		$img = getimagesize ( $png );
		$cla = ($this->isIE () && $this->BrowserVersion < 7) ? "filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src='$png', sizingMethod='image');" : 'background:url("' . $png . '") 0 0 no-repeat;';
		$cla .= ' width:' . $img [0] . 'px; height:' . $img [1] . 'px;';
		return $cla;
	}

}
?>