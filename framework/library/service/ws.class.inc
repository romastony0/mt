<?php
/**
 * Web service response class
 * status -> 0 for success, 1 for failure.
 * result -> on success, contains result object.
 * on failure, contains error string or object.
 */
@define ( 'WS_SUCCESS', true );
@define ( 'WS_FAIL', false );

class WSResponse {
	public $success;
	public $message;
	
	function WSResponse($result) {
		$this->success = WS_FAIL;
		$this->message = $result;
	}
	
	function setError($result) {
		$this->success = WS_FAIL;
		$this->message = $result;
	}
	
	function setResult($result) {
		$this->success = WS_SUCCESS;
		$this->message = $result;
	}
}


?>