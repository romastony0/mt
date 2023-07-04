<?php
/**
 * Created by Vizuamatix (pvt) Ltd.
 * web www.vizuamatix.com.
 */

require_once('MyCript.php');

class Vcg {

    private $vendorId = '';
    private $vendorKey = '';
    private $vrsn = '';
    private $vpd = '';
    private $vrt = '';
    private $msisdn = '';
    private $transactionId = '';
    private $amount = '';
    private $contentType = '';
    private $contentId = '';
    private $subscriptionType = '';
    private $url = '';

    public function __construct(){
        $this->setVRSN();
    }

    public function setVcgUrl($url){
        $this->url = $url;
    }

    public function setVendorId($vendorId){
        $this->vendorId = $vendorId;
    }

    public function setVendorKey($vendorKey){
        $this->vendorKey = $vendorKey;
    }

    private function setVRSN(){
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < 10; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        $this->vrsn =  $randomString;
    }

    private function setVPD(){
        $vendor_id = $this->vendorId;
        $nonce = $this->vrsn;
        $timeStamp  = $this->vrt;
        $vpd = substr($nonce,0,1);
        $vpd .= (intval(substr($timeStamp,0,2)) + 568);
        $vpd .= substr($nonce,-1,1);
        $vpd .= (intval(substr($timeStamp,5,2)) + 108)%2;
        $vpd .= $nonce;
        $vpd .= (intval(substr($timeStamp,-1,2)) - 5);
        $vpd .= $vendor_id;
        $vpd .= substr($timeStamp, 6,1);
        $vpd .= substr($nonce,5,1);
        $vpd .= (intval(substr($timeStamp,4,3)) + 91);
        $vpd .= substr($nonce,3,1);
        $vpd .= round(((intval($timeStamp)) + 147)/89);
        $this->vpd =  $vpd;
    }

    private function setVRT(){
        $oCurrentTime = new DateTime("now", new DateTimeZone('Asia/Colombo'));
        $currentTime = $oCurrentTime->format('Y-m-d H:i:s');
        $this->vrt = strtotime($currentTime);
    }

    public function setMSISDN($msisdn){
        $this->msisdn = $msisdn;
    }

    public function setTransactionId($transactionId){
        $this->transactionId = $transactionId;
    }

    public function setAmount($amount){
        $this->amount = $amount;
    }

    public function setContentType($contentType){
        $this->contentType = $contentType;
    }

    public function setContentId($contentId){
        $this->contentId = $contentId;
    }

    public function setSubscriptionType($subscriptionType){
        $this->subscriptionType = $subscriptionType;
    }

    public function setInitialize(){
        $this->setVRT();
        $this->setVPD();
        $aData = array(
                    'vendorId'=>$this->vendorId,
                    'vrsn'=>$this->vrsn,
                    'vpd'=>$this->vpd,
                    'vrt'=>$this->vrt,
                    'msisdn'=>$this->msisdn,
                    'transactionId'=>$this->transactionId,
                    'amount'    =>  $this->amount,
                    'contentType'   =>  $this->contentType,
                    'contentId'     =>  $this->contentId,
                    'subscriptionType' => $this->subscriptionType,
        );
		
		//print_r($aData);


        $oCrypt = new MyCript();
        $oJsonData = json_encode($aData);
        $iCryptData = $oCrypt->vcgEncrypt($oJsonData, $this->vendorKey, substr($this->vpd,0,16));die;
        $aData = array('data'=>$iCryptData, 'vendorId'=>$this->vendorId, 'vpd'=>$this->vpd);
        $oJsonData = json_encode($aData);
        $iCryptData = $oCrypt->encrypt($oJsonData);
        $oResult = $this->httpRequestSender($iCryptData);
        $aResult = json_decode($oResult);
		
        return $aResult;

    }

    private function httpRequestSender($data){

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$this->url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,
            "params=".$data);

        // receive server response ...
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYSTATUS, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		
        $server_output = curl_exec ($ch);
        curl_close ($ch);
        return $server_output;
        // print_r(json_decode($server_output));
    }

}