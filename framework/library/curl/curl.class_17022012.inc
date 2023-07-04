<?php
class CurlLibrary {
	function doRequest($method, $url, $vars) {
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
	    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    if ($method == 'POST') {
	        curl_setopt($ch, CURLOPT_POST, 1);
	        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
	        
	        curl_setopt($ch, CURLOPT_POSTFIELDS, $vars);
	    }
	    $data = curl_exec($ch);
	    
	    if ($data) {
	       console(LOG_LEVEL_INFO,'Response  '.$data);
	       curl_close($ch);
	       return $data;
	    } else {
	    	console(LOG_LEVEL_ERROR,'Unable to Fetch url. Error is  '.curl_error($ch));
	    	curl_close($ch);
	        return false;
	    }
	}
	
	function get($url) {
		console(LOG_LEVEL_INFO,'URL get '.$url);
	    return $this->doRequest('GET', $url, 'NULL');
	}
	
	function post($url, $vars) {
		console(LOG_LEVEL_INFO,'URL post '.$url.$vars);
	    return $this->doRequest('POST', $url, $vars);
	}
}
?>