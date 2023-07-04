<?php
class CurlLibrary {
	function doRequest($method, $url, $vars, $isXml=false, $errorCode=false) {
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
	    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	    $scheme = parse_url($url, PHP_URL_SCHEME);
	    if($scheme == 'https')
	    {
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,0);
	    }
	    // connection timeout if not 5050 / 7070 port
	    $portNumber = parse_url($url, PHP_URL_PORT);
	    if($portNumber == '5050' || $portNumber == '7070') {
	    	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,120);
	    }
	    else {
	    	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,10);
	    }
	    curl_setopt($ch, CURLOPT_TIMEOUT,600);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    if ($method == 'POST') {
	        curl_setopt($ch, CURLOPT_POST, 1);
	        if($isXml === true) {
	        	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
	        }
	        
	        curl_setopt($ch, CURLOPT_POSTFIELDS, $vars);
	    }
		$time_start = microtime(true);
	    $data = curl_exec($ch);
	    $retCode = curl_getinfo($ch);
		$time_end = microtime(true);
		$time = round(($time_end - $time_start),4);	 
	    
	    if ($data) {
	       console(LOG_LEVEL_INFO,'Request URL : '.$url.' | Request data : '.$vars.' | Response  '.$data.' | Response Time in Seconds: '.$time);
	       if($errorCode === false) {
	       		curl_close($ch);
	       		return $data;
	       } else {	       		
	       		console(LOG_LEVEL_INFO,'Curl Informations : '. var_export($retCode,true));
	       		curl_close($ch);
	        	return array(true,$retCode['http_code']);
	       }	       
	    } else {
	    	console(LOG_LEVEL_ERROR,'Request URL : '.$url.' | Request data : '.$vars.' | Unable to Fetch url. Error code is '.curl_errno($ch).' |  Error is  '.curl_error($ch));
	    	if($errorCode === false) {
	    		curl_close($ch);
	        	return false;
	    	} else {
	    		console(LOG_LEVEL_INFO,'Curl Informations : '. var_export($retCode,true));	        	
	        	curl_close($ch);
	        	return array(false,$retCode['http_code']);
	        }	        
	    }
	}
	
	function get($url) {
		console(LOG_LEVEL_INFO,'URL get '.$url);
	    return $this->doRequest('GET', $url, 'NULL');
	}
	
	function post($url, $vars,$isXml = true, $errorCode = false) {
		console(LOG_LEVEL_INFO,'URL post '.$url.$vars);
	    return $this->doRequest('POST', $url, $vars, $isXml, $errorCode);
	}
}
?>
