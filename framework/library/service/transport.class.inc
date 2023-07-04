<?php
/**
 * data transport class
 * status -> 0 for success, 1 for failure.
 * result -> on success, contains result object.
 * on failure, contains error string or object.
 */
@define ( 'TP_SUCCESS', true );
@define ( 'TP_FAIL', false );



class Transport {
	private $_data = array();
	
	function Transport() {
		$this->success = false;
	}
	
	function setValue($id,$value) {
		$this->_data[$id]=$value;
	}
	
	function setError($result) {
		$this->_data['success'] = TP_FAIL;
		$this->_data['result'] = $result;
	}
	
	function setTransId($transid)
	{
		$this->_data['transactionid'] = $transid;
	}
	
	function setResult($result,$action='') {
		$this->_data['success'] = TP_SUCCESS;
		if($action){
			$cnt = count($result);
			$this->_data['total']  = "$cnt";
		}
 		$this->_data['result'] = $result;
	}
	
	function getValue($id) {
		return $this->_data[$id];
	}
	
	function arrToXML(){
		global $library,$libxml;
		$library->loadLibrary('xml');
		$tmp_trans_arr = array();
		if( $this->_data['success'])
		{
			$this->_data['success'] = 'true';
		}
		else {
			$this->_data['success'] = 'false';
		}
		$tmp_trans_arr['response'] = $this->_data;
		return $libxml->getXML($tmp_trans_arr);
	}
	
	function arrToJSON(){
		return $json_response = json_encode($this->_data);
	}
	
	function getdefault(){
		return $this->_data;
	}
}
?>