 <?php
// define("DB_CONNECTIONS","SLAVE_INTDEXTER");
ini_set('display_errors', 1); 
include_once("../framework/initialise/framework.init.php");
include_once("functions.php");
global $library,$request,$db,$curl;
$library->loadLibrary ( "curl" );
console(LOG_LEVEL_TRACE, "Incoming Request ::".var_export($request, true));
$currentdatetime = date("Y-m-d H:i:s");
$callback_val['action'] = $request['action'];
$callback_val['appid'] = $request['appid'];
$callback_val['msisdn'] = $request['msisdn'];
// $callback_val['startdate'] = date("Y-m-d H:i:s",strtotime($request['startdate']));
// $callback_val['expirydate'] = date("Y-m-d H:i:s",strtotime($request['expirydate']));
// $callback_val['status'] = $request['status'];
// $callback_val['validdays'] = $request['validdays'];
// $callback_val['keyword'] = $request['keyword'];
$callback_val['operator'] = $request['operator'];
$callback_val['amount'] = $request['amount'];
$callback_val['transaction'] = $request['transaction'];
$callback_val['packtype'] = $request['typepack'];
// $callback_val['productid'] = $request['productid'];
echo "Successfully called postback file";
$postbackurl=SUBMANAGER_URL."gateway/postback_callback.php?appid=".$callback_val['appid']."&msisdn=".$callback_val['msisdn']."&operator=".$callback_val['operator']."&amount=".$callback_val['amount']."&transaction=".$callback_val['transaction']."&channelid=wap";
$callback_res = $curl->get($postbackurl);
?>
