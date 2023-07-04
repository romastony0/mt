<?php

/**
 * Ago calculator
 * http://www.celticproductions.net/articles/5/php/x-y+ago.html
 *
 * @param unknown_type $datefrom
 * @param unknown_type $dateto
 * @return unknown
 */
function xyago($datefrom, $dateto = -1) {
	// Defaults and assume if 0 is passed in that
	// its an error rather than the epoch
	

	if ($datefrom == 0) {
		return "A long time ago";
	}
	if ($dateto == - 1) {
		$dateto = time ();
	}
	
	// Calculate the difference in seconds betweeen
	// the two timestamps
	

	$difference = $dateto - $datefrom;
	
	// If difference is less than 60 seconds,
	// seconds is a good interval of choice
	

	if ($difference < 60) {
		$interval = "s";
	} 

	// If difference is between 60 seconds and
	// 60 minutes, minutes is a good interval
	elseif ($difference >= 60 && $difference < 60 * 60) {
		$interval = "n";
	} 

	// If difference is between 1 hour and 24 hours
	// hours is a good interval
	elseif ($difference >= 60 * 60 && $difference < 60 * 60 * 24) {
		$interval = "h";
	} 

	// If difference is between 1 day and 7 days
	// days is a good interval
	elseif ($difference >= 60 * 60 * 24 && $difference < 60 * 60 * 24 * 7) {
		$interval = "d";
	} 

	// If difference is between 1 week and 30 days
	// weeks is a good interval
	elseif ($difference >= 60 * 60 * 24 * 7 && $difference < 60 * 60 * 24 * 30) {
		$interval = "ww";
	} 

	// If difference is between 30 days and 365 days
	// months is a good interval, again, the same thing
	// applies, if the 29th February happens to exist
	// between your 2 dates, the function will return
	// the 'incorrect' value for a day
	elseif ($difference >= 60 * 60 * 24 * 30 && $difference < 60 * 60 * 24 * 365) {
		$interval = "m";
	} 

	// If difference is greater than or equal to 365
	// days, return year. This will be incorrect if
	// for example, you call the function on the 28th April
	// 2008 passing in 29th April 2007. It will return
	// 1 year ago when in actual fact (yawn!) not quite
	// a year has gone by
	elseif ($difference >= 60 * 60 * 24 * 365) {
		$interval = "y";
	}
	
	// Based on the interval, determine the
	// number of units between the two dates
	// From this point on, you would be hard
	// pushed telling the difference between
	// this function and DateDiff. If the $datediff
	// returned is 1, be sure to return the singular
	// of the unit, e.g. 'day' rather 'days'
	

	switch ($interval) {
		case "m" :
			$months_difference = floor ( $difference / 60 / 60 / 24 / 29 );
			while ( mktime ( date ( "H", $datefrom ), date ( "i", $datefrom ), date ( "s", $datefrom ), date ( "n", $datefrom ) + ($months_difference), date ( "j", $dateto ), date ( "Y", $datefrom ) ) < $dateto ) {
				$months_difference ++;
			}
			$datediff = $months_difference;
			
			// We need this in here because it is possible
			// to have an 'm' interval and a months
			// difference of 12 because we are using 29 days
			// in a month
			

			if ($datediff == 12) {
				$datediff --;
			}
			
			$res = ($datediff == 1) ? "$datediff month ago" : "$datediff
			months ago";
			break;
		
		case "y" :
			$datediff = floor ( $difference / 60 / 60 / 24 / 365 );
			$res = ($datediff == 1) ? "$datediff year ago" : "$datediff
			years ago";
			break;
		
		case "d" :
			$datediff = floor ( $difference / 60 / 60 / 24 );
			$res = ($datediff == 1) ? "$datediff day ago" : "$datediff
			days ago";
			break;
		
		case "ww" :
			$datediff = floor ( $difference / 60 / 60 / 24 / 7 );
			$res = ($datediff == 1) ? "$datediff week ago" : "$datediff
			weeks ago";
			break;
		
		case "h" :
			$datediff = floor ( $difference / 60 / 60 );
			$res = ($datediff == 1) ? "$datediff hour ago" : "$datediff
			hours ago";
			break;
		
		case "n" :
			$datediff = floor ( $difference / 60 );
			$res = ($datediff == 1) ? "$datediff minute ago" : "$datediff minutes ago";
			break;
		
		case "s" :
			$datediff = $difference;
			$res = ($datediff == 1) ? "$datediff second ago" : "$datediff seconds ago";
			break;
	}
	return $res;
}

/**
 * Truncate with ellipsis.
 * From: http://in.php.net/substr_replace
 *
 * @param unknown_type $text
 * @param unknown_type $numb
 * @return unknown
 */
function truncate($text, $numb) {
	$text = html_entity_decode ( $text, ENT_QUOTES );
	if (strlen ( $text ) > $numb) {
		$text = substr ( $text, 0, $numb );
		$text = substr ( $text, 0, strrpos ( $text, " " ) );
		//This strips the full stop:
		if ((substr ( $text, - 1 )) == ".") {
			$text = substr ( $text, 0, (strrpos ( $text, "." )) );
		}
		$etc = "...";
		$text = $text . $etc;
	}
	$text = htmlentities ( $text, ENT_QUOTES );
	return $text;
}

//echo xyago(time()-3600*24);


function resizejpeg($dir, $img, $max_w, $max_h) {
	// get original images width and height
	list ( $or_w, $or_h, $or_t ) = getimagesize ( $dir . $img );
	// make sure image is a jpeg
	if ($or_t == 2) {
		// obtain the image's ratio
		$ratio = ($or_h / $or_w);
		// original image
		$or_image = imagecreatefromjpeg ( $dir . $img );
		// resize image
		if ($or_w > $max_w || $or_h > $max_h) {
			// first resize by width (less than $max_w)
			if ($or_w > $max_w) {
				$rs_w = $max_w;
				$rs_h = $ratio * $max_h;
			} else {
				$rs_w = $or_w;
				$rs_h = $or_h;
			}
			// then resize by height (less than $max_h)
			if ($rs_h > $max_h) {
				$rs_w = $max_w / $ratio;
				$rs_h = $max_h;
			}
			// copy old image to new image
			$rs_image = imagecreatetruecolor ( $rs_w, $rs_h );
			imagecopyresampled ( $rs_image, $or_image, 0, 0, 0, 0, $rs_w, $rs_h, $or_w, $or_h );
		} else {
			$rs_w = $or_w;
			$rs_h = $or_h;
			$rs_image = $or_image;
		}
		// generate resized image
		imagejpeg ( $rs_image, $dir . 'thumb_' . $img, 100 );
		return true;
	} // Image type was not jpeg!
else {
		return false;
	}
}

function cleansql($str) {
	$characters = array ('/\x00/', '/\x1a/', '/\\\/', '/\'/' );
	$replace = array ('\\\x00', '\\x1a', '\\\\', "''" );
	return preg_replace ( $characters, $replace, $str );
}

function cleanvals($myval) {
	if (is_array ( $myval )) {
		foreach ( $myval as $key => $val ) {
			$myval [$key] = cleanvals ( $val );
		}
		return $myval;
	}
	$badbadsql = "(declare)|(cast)|(drop)";
	$myval = eregi_replace ( $badbadsql, "", $myval );
	if (get_magic_quotes_gpc ()) {
		//get rid of triple slashes mysql_real_escape_string would create
		$myval = stripslashes ( $myval );
	}
	return $myval;
}

function cleanup($myinput) {
	if (is_array ( $myinput )) {
		foreach ( $myinput as $key => $val ) {
			$myinput [$key] = cleanvals ( $val );
		}
		return array_map ( "cleansql", $myinput );
	} else {
		$myinput = cleanvals ( $myinput );
		return cleansql ( $myinput );
	}
}

function cleantext($text) {
	//returns safe code for preloading in the RTE
	//convert all types of single quotes
	$text = str_replace ( chr ( 145 ), chr ( 39 ), $text );
	$text = str_replace ( chr ( 146 ), chr ( 39 ), $text );
	$text = str_replace ( "'", "&#39;", $text );
	//convert all types of double quotes
	$text = str_replace ( chr ( 147 ), chr ( 34 ), $text );
	$text = str_replace ( chr ( 148 ), chr ( 34 ), $text );
	$text = str_replace ( "\"", "&#34;", $text );
	//replace carriage returns & line feeds
	$text = str_replace ( chr ( 10 ), " ", $text );
	$text = str_replace ( chr ( 13 ), " ", $text );
	//replace html tags
	$text = str_replace ( "<", "&lt;", $text );
	$text = str_replace ( ">", "&gt;", $text );
	
	return $text;
}

function getMysqlDateFormat($dateformat) {
	$formats = array ('m/d/Y H:i:s' => '%m/%d/%Y %H:%i:%s', 'm/d/Y h:i:s A' => '%m/%d/%Y %h:%i:%s %p', 'm/d/Y H:i' => '%m/%d/%Y %H:%i', 'm/d/Y h:i A' => '%m/%d/%Y %h:%i %p', 'd/m/Y H:i:s' => '%d/%m/%Y %H:%i:%s', 'd/m/Y h:i:s A' => '%d/%m/%Y %h:%i:%s %p', 'd/m/Y H:i' => '%d/%m/%Y %H:%i', 'd/m/Y h:i A' => '%d/%m/%Y %h:%i %p', 'Y-m-d H:i:s' => '%Y-%m-%d %H:%i:%s', 'Y-m-d h:i:s A' => '%Y-%m-%d %h:%i:%s %p', 'Y-m-d H:i' => '%Y-%m-%d %H:%i', 'Y-m-d h:i A' => '%Y-%m-%d %h:%i %p', 'M d, Y H:i:s' => '%b %d, %Y %H:%i:%s', 'M d, Y h:i:s A' => '%b %d, %Y %h:%i:%s %p', 'M d, Y H:i' => '%b %d, %Y %H:%i', 'M d, Y h:i A' => '%b %d, %Y %h:%i %p' );
	//print_r($formats);
	return ($formats [$dateformat] == '') ? $formats ['m/d/Y H:i:s'] : $formats [trim ( $dateformat )];
}

function is_empty($var, $allow_false = false, $allow_ws = false) {
	if (! isset ( $var ) || is_null ( $var ) || ($allow_ws == false && trim ( $var ) == "" && ! is_bool ( $var )) || ($allow_false === false && is_bool ( $var ) && $var === false) || (is_array ( $var ) && empty ( $var ))) {
		return true;
	} else {
		return false;
	}
	
//unset variable ($notset) - Empty: yes
//null - Empty: yes
//0 - Empty: no
//string "0" - Empty: no
//false - Empty: yes
//false ($allow_false = true) - Empty: no
//true - Empty: no
//string "foo" - Empty: no
//white space " " - Empty: yes
//white space ($allow_ws = true) " " - Empty: no
//empty array - Empty: yes		
}
?>
