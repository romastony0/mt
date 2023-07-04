<?php
			
	/* Request XML Data Sample
	 
	
	<Message>
  <Version Version="1.0"/>
  <Request Type="OBSRequest" RefNo="1">         RefNo --> Random number generation , Type is always obsrequest
    <UserID>C5Test01</UserID>                   ---- ini config
    <Password>symbi03</Password>				---- ini config
    <OBSRequest Validity="****A*****"           ---- its default value is '00020000'
                Flags="****B*****">             ---- its default value is '32' 
      <Ticket Type="Mobile"						---- Currently always needs to be set to "Mobile". 
              OBSService=""						---- Leave blank
              Service="C5T01"					---- Service name
              SubService=""						---- Leave blank
              ChargeAddr="0829034444"			---- msisdn
              Description="TEST PRODUCT"		---- Service description, which is TEST PRODUCT
              Value="500"						---- Charge amount, which is 200 for the configured service
			  Currency="ZAR"/>					--- Currency code,  which is ZAR
      <Subscr Started="2014-06-27 15:59:52 FIRST" 	Started --- subscribtion date started
              Category="YOURPRODUCTPORTALSERVICE"   'First' ---> First Time subscribtion , 'STOP' ----> Stop the service, ''-----> if it empty ,it means autorenewal
              Trigger=""/>						---- Leave Blank
    </OBSRequest>
  </Request>
</Message> 

CLI ==> service send no

Ex : start joke 123juk to 123456
Here 123456 is CLI
Joke - service name
123juk --- key word

*/	

	
	/*{"transacitonid":"12377123bxcvb235wr","msisdn":66765434321,"keyword":"STOPGAME","
productid
":419010501,"type":"RENEWAL","chargeamount":10,"responsecode":"201"}*/





   $SampleJsonarr = array('transactionid' => '12377123bxcvb235wr','operator'=>'cellc', 'msisdn' => '27835146969', 'keyword' => 'INIT RTW','productid'=>'419010501','type' =>'SUBSCRIPTION','mode'=>'WAP', 'chargeamount' => '5','startdate'=>'2014-10-21 13:35:28','enddate'=>'2014-10-23 13:35:28','responsecode'=>'200');
   
   print_r($SampleJsonarr);
   
      $responseJSON = json_encode($SampleJsonarr);
   $ArrJsonDecoded = json_decode( $responseJSON, TRUE );
   /*
   $ArrJsonEncoded = file_get_contents('php://input'); 
   $ArrJsonDecoded = json_decode($ArrJsonEncoded);
   
   */
   //$ArrJsonDecoded 	 = json_decode(file_get_contents('php://input'), true);
   $varRefId = $varTransactionId = $ArrJsonDecoded['transactionid'];
   $varOperator		 = $ArrJsonDecoded['operator'];
   $varMSISDN		 = $ArrJsonDecoded['msisdn'];
   $varKeyWord		 = $ArrJsonDecoded['keyword'];
   $varProductId 	 = $ArrJsonDecoded['productid'];
   $varType			 = $ArrJsonDecoded['type'];
   $varMode		 	 = $ArrJsonDecoded['mode'];
   $varChargeAmount	 = $ArrJsonDecoded['chargeamount'];
   $varStartDate 	 = $ArrJsonDecoded['startdate']; 
   $varEndDate 	 	 = $ArrJsonDecoded['enddate']; 
   $varResponseCode	 = $ArrJsonDecoded['responsecode'];  
   $varSystemRandomNo =  $log->getNewTransactionId();
  //$varSystemRandomNo  = rand();
     
   
   print_r($ArrJsonDecoded);
	require_once("initialise.php");
	mysql_set_charset('utf8');
	$content_id_delivered = '';
	//$request ['cli'] = trim ( $request ['cli'] ) ;
	//$request ['msisdn'] =  trim ( $request ['msisdn'] ) ;	
	$varSubStartStop = '';
	
	//  start here
	$query = "INSERT INTO audit (msisdn,ipaddress,inputs)	VALUES ('" . $varMSISDN. "','".$_SERVER ['REMOTE_ADDR'] ."','" . mysql_real_escape_string ( var_export ( $ArrJsonDecoded, true ) ) . "')";
	$db->query ( $query );	
	
	

	
	
	if ($varTransactionId == "" || $varKeyWord == "" || $varMSISDN == "" || $varType == "" || $varMode == "" || $varChargeAmount=="" || $varStartDate=="" || $varResponseCode=="") {
		echo 'Malformed query! Please try again.';
		console ( LOG_LEVEL_ERROR, var_export ( $ArrJsonDecoded, true ) );
		exit ( - 1 );
	}

	
	
	//checks wheather subscription or unsubscription
	
	console ( LOG_LEVEL_INFO, "Subscription Request INFO - MSISDN :".$varMSISDN." , KEYWORD:".$varKeyWord." ,MODE:".$varMode."  , MO CLI:".$cli );
	try{
	
	
	echo "After the select query of  keyword";
	print_r($varKeyWordDetails);
			
		
	if ($varType == "UNSUBSCRIPTION" ){
		$key_type = 'UNSUBSCRIPTION';
		$varKeyWordDetails = $db->getOneRow ( "SELECT *  FROM keyword k,cli c WHERE k.unsubscribekey = '" . $varKeyWord . "' AND k.status='active' AND k.idkeyword = c.idkeyword AND c.status = 'active'" );
	
		}
		
	if( $varType == "SUBSCRIPTION" ){
		$key_type = 'SUBSCRIPTION';	
		$varKeyWordDetails = $db->getOneRow ( "SELECT *  FROM keyword k,cli c WHERE k.subscribekey = '" . $varKeyWord . "' AND k.status='active' AND k.idkeyword = c.idkeyword AND c.status = 'active'" );
	
		}
	echo 'varType::'.$key_type;		


	$logKeytype = ($key_type != false)?$key_type:"Invalid keyword (".$varKeyWord.")";

	console ( LOG_LEVEL_INFO, "[ MSISDN :".$varMSISDN." ] requested keyword type : ".$logKeytype );
	
$TransactionDetail = $db->query("INSERT INTO transaction(transactionid,msisdn,cli,mode,type,keyword) VALUES ('". $varTransactionId ."','". $varMSISDN."','". $varKeyWordDetails ['serviceid'] ."','". $varMode ."','". $key_type ."','". $varKeyWord ."')" );
	

	//checking for a valid keyword.
	if( !$varKeyWordDetails )  {
		$response = RESPONSE_INVALID_KEYWORD;
		$responseCLI = $cli;
		//$reqstatflag = 'unknown keyword';
		$responsecode = CODE_UNKNOWN_KEYWORD;
		console ( LOG_LEVEL_INFO, " [ MSISDN :".$varMSISDN." ]  Keyword details not found ! | Response:".$response );
		//pushContent( $varMSISDN , $response , $responseCLI );
		
		echo "Not Valid KeyWord found";

	} 
	elseif( $varKeyWordDetails ) {//for subscription and unsubsciption
	
	//print "SELECT idsubscription FROM subscription WHERE varMSISDN = '".$varMSISDN."' AND idkeyword = ". $varKeyWordDetails['idkeyword'] ." AND (status = 'active' or status = 'suspended')<br>";
		//$varMSISDNEntry = $db->getOneRow( "SELECT idsubscription,subscribemode FROM subscription WHERE varMSISDN = '".$varMSISDN."' AND idkeyword = ". $varKeyWordDetails['idkeyword'] ." AND customerid =  '".$customerid."' AND (status = 'active' or status = 'suspended')" );
		
		echo "<br>subscription loop1";
		
		
		$query = "INSERT INTO motrace (mode,msisdn,serviceid,operator_name,refid)	VALUES ('" . $varMode . "','" . $varMSISDN. "','".$varKeyWordDetails ['serviceid']."','" . $varOperator. "','".$varSystemRandomNo."')";
	
		$db->query ( $query );
		echo "<br>After motrace insert";
		
		$varMSISDNEntry = $db->getOneRow( "SELECT idsubscription,subscribemode FROM subscription WHERE msisdn = '".$varMSISDN."' AND idkeyword = ". $varKeyWordDetails['idkeyword'] ." AND (status = 'active' or status = 'suspended')" );
		echo "<br>After select from subscription";
			
		if ($key_type == "SUBSCRIPTION"){
		echo "<br>subscription loop2";
			$varSubStartStop = 'FIRST';
		
			//echo "subscription";
			//print_r($varMSISDNEntry);
			if ( !$varMSISDNEntry ) { // For new subscription requests
				//$newSubscriptionMo = "INSERT INTO subscription(idkeyword,varMSISDN,customerid,subscribemode,status,linkedid) VALUES (". $varKeyWordDetails['idkeyword'] .",'". $varMSISDN ."','". $customerid ."','". $varMode ."','suspended','". $linkedid ."')";
				
				echo "New subscription";
				
				$newSubscriptionMo = "INSERT INTO subscription(idkeyword,msisdn,operator_name,subscribemode,status) VALUES (". $varKeyWordDetails['idkeyword'] .",'". $varMSISDN ."','" . $varOperator. "','". $varMode ."','suspended')";
				$db->query( $newSubscriptionMo );
				$subscribeId = $db->getLastID ();			
				


				
				if($varKeyWordDetails['charging'] == "yes"){
				//echo "charging";
				echo "<br>After insert subscription";
					$chargingResponse = chargingSubscription( $varMSISDN , $varKeyWordDetails['idkeyword'],$varKeyWordDetails['unsubscribekey'],$varKeyWordDetails['service_name'] ,$varKeyWordDetails['unsub_mo_cli']); 
				
					if( $chargingResponse ){
					
						
						$query = "INSERT INTO chargingtransaction(productid,subscriptionid,refid,charging_amount,charging_cli,validity_period,type) values ('". $varProductId ."',". $subscribeId .",".$varSystemRandomNo.",'". $chargingResponse['amount'] ."','". $chargingResponse['cli'] ."','". $chargingResponse['validity'] ."','subscription')";
						$db->query ( $query );
						
						// inserting in subscription table entries
						$endDate = "DATE_ADD( '".$varEndDate."' , INTERVAL ".($chargingResponse ['validity'])." DAY ) ";
						$srtDate = "DATE_ADD( '".$varStartDate."' , INTERVAL ".($chargingResponse ['validity'])." DAY ) ";
						
						
						echo "endDate::".$endDate;
						echo "renewalDate::".$renewalDate;
						
						
				
						$subhistory_query = "INSERT INTO subscriptionhistory (`idsubscription`,`renewalon`,`billedamount`,`validityperiod`,`startdate`,`enddate`, `rennotification`)
											VALUES (" . $subscribeId . ",
												".$endDate .",
												'" .$chargingResponse['amount']. "',
												'" .$chargingResponse ['validity'] . "',
												".$srtDate.",
												".$endDate.",0)";
						$res = $db->query ( $subhistory_query );
						//print_r($chargingResponse);
						echo "<br>After insert chargingtransaction";
						if( $chargingResponse['validity'] != '1' ){
						
							$response = $chargingResponse ['MTmessage'];
							echo "validity!=1";							
							
							$responsecode = CODE_SUBSCRIPTION_SUCCESSFULL;
							
						}elseif( $chargingResponse['validity'] == '1' ){
							echo "<br>validity==1-- UPDATE subscription SET billed = 'unbilled' WHERE idsubscription";
							$response = 'โปรดรอสักครู่ กำลังดำเนินการ';
							$responsecode = CODE_SUBSCRIPTION_SUCCESSFULL;							
							$db->query ( "UPDATE subscription SET billed = 'unbilled' WHERE idsubscription =".$subscribeId );							
						}
						$sender = $varKeyWordDetails ['subs_mo_cli'];
					}else{
						$response = RESPONSE_UNABLE_TO_PROCESS_CHARGING;
						
						$responsecode = CODE_UNABLE_TO_PROCESS_CHARGING;
						
						echo "<br>unable to process---UPDATE subscription SET billed = 'subno' WHERE idsubscription";
						
						$sender = $varKeyWordDetails ['subs_mo_cli'];
						$db->query( "UPDATE subscription SET billed = 'subno' WHERE idsubscription = ".$subscribeId );
					}
				}else{
					$db->query( "UPDATE subscription SET billed = 'never' WHERE idsubscription = ".$subscribeId );
					
					$search = array ("~keyword~","~unsubkey~","~mocli~");
					$replace = array ($serviceName , $varKeyWordDetails['unsubscribekey'], $varKeyWordDetails['unsub_mo_cli']);
					$message = str_replace ( $search, $replace, RESPONSE_SUBSCRIPTION_FREE_SUCESSFULL );					
					$response = $message;
					$responsecode = CODE_SUBSCRIPTION_SUCCESSFULL;

					echo "UPDATE subscription SET billed = 'never' WHERE idsubscription ";
					$sender = $varKeyWordDetails ['subs_mo_cli'];
				}
			}else{
				$response = RESPONSE_ALREADY_SUBSCRIBED;
				
				
				$responsecode = CODE_ALREADY_SUBSCRIBED;				
				$sender = $varKeyWordDetails ['subs_mo_cli'];
				$subscribeId = $varMSISDNEntry['idsubscription'];
			}
			
			echo "Response message in contentq table::".$sender;
			pushContent( $varMSISDN , $response , $sender );
		}elseif($key_type == "UNSUBSCRIPTION")
		{
			if(!$varMSISDNEntry){
				
				$response = RESPONSE_UNSUBSCRIPTION_UNSUCESSFULL;
				
				$responsecode = CODE_UNSUBSCRIPTION_FAILED;
				
				
				echo "code unsubscribe failed";
				$sender = $varKeyWordDetails ['subs_mo_cli'];
			}else
			{
			
				$varSubStartStop = 'STOP';
				$subscribeId = $varMSISDNEntry['idsubscription'];
				$sub_charged = $db->getOneRow("select * from subscription where idsubscription= '".$varMSISDNEntry['idsubscription']."' and status ='suspended'");
				
				echo "code unsubscribe mode";
				
				$unsubQuery = "UPDATE subscription SET status='inactive', unsubscribedon = CURRENT_TIMESTAMP,unsubscribemode = '".$varMode."' WHERE msisdn = '".$varMSISDN."' AND idkeyword = ".$varKeyWordDetails['idkeyword']." AND status IN ('active','suspended')";
				$db->query($unsubQuery);
								
				$response = $varKeyWordDetails ['unsub_content'];
				
				$responsecode = CODE_UNSUBSCRIPTION_SUCCESSFULL;				
							
				//WAP unsub notification
				
					$servicetype="UNSUBSCRIPTION"; 				
					
				/* WAP XML generation code start */		
				
				
				/*  XML generation code start */		
				
				
				
				$varXMLCode = '<?xml version="1.0" encoding="UTF-8"?>
									<Message><Request Type="OBSRequest" RefNo="'.$varSystemRandomNo .'">
									<UserID>'.INTEGRAT_USERNAME.'</UserID>
									<Password>'.INTEGRAT_PASSWORD.'</Password>
									<OBSRequest Validity="00020000" Flags="32">
									<Ticket Type="Mobile"
												  OBSService=""
												  Service="C5T01"					
												  SubService=""
												  ChargeAddr="'.$varMSISDN.'"			
												  Description="Test Procuct"		
												  Value=""					
												  Currency="'.INTEGRAT_CURRENCY.'"/>
									<Subscr Started="'.$varStartDate.' ' .$varSubStartStop.'" 	
												  Category="'.$varKeyWordDetails ['category'].'"   
												  Trigger=""/>
									</OBSRequest></Request></Message>';
				echo $varXMLCode;
				
				

				/*XML generation code end */
				
				
				//$integrat_subscription_post = "http://xhg-lb1.higate.co.za:8888/hg_request/"
				// post xml to this request URL					
					
					//CURL
					
					echo "CURL INIT for unsub";
				
					$c = curl_init();
					curl_setopt($c, CURLOPT_URL,INTEGRAT_SUBSCRIPTION_NOTIFICATION_URL);
					curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($c, CURLOPT_POSTFIELDS, $varXMLCode );
					curl_setopt($c, CURLOPT_HTTPHEADER, array('Content-Type: application/xml'));
					echo "CURL END";
					
					$result = curl_exec($c);
					echo "CURL END - unsub";
					print_r($result);
			
					console ( LOG_LEVEL_TRACE, 'WAP ('.$servicetype.') Un-Subscription notification to WAP team ---> request:'.$varWapXMLCode.' || Response ( '.$result);
						
							
				
				
				
			}
		}
		console ( LOG_LEVEL_INFO, " [ MSISDN :".$varMSISDN." ]  ".$keytype." | MO Response : ".$response );
		
	}
	
	
		$transCnd = ( $subscribeId )? ", flagid = ".$subscribeId : "";
		

	
	$finalout ['message'] = $response;
	$finalout ['responsecode'] = $responsecode;
	$finalout ['transactionid'] = $transaction;
	
	$resout [0] = $finalout;
	
	$db->query( "UPDATE transaction SET returncode = ". $finalout ['responsecode'] ." , returnmsg = '".mysql_real_escape_string($finalout ['message'])."' ". $transCnd ." WHERE transactionid = '". $varTransactionId ."'" );
	
	
	
	//echo json_encode ( $finalout );
	
} catch ( Exception $ex ) {
	console ( LOG_LEVEL_ERROR, $ex->getMessage () );
}
	

	function chargingSubscription( $varMSISDN , $varKeyWordId ,$unsubKey , $serviceName ,$mocli)	//Charging subscription 
	{
		global $restRequest, $db,$transaction,$varKeyWordDetails,$varStartDate,$varChargeAmount,$varKeyWord,$varSystemRandomNo,$varSubStartStop;
		echo "Service Id ::".$varKeyWordDetails['serviceid'];
		
		$charging_CLI = "";$returnVal = false;
		$result = $db->getOneRow( "SELECT * FROM keyworddetails WHERE keywordid=".$varKeyWordId." AND recordstatus='active' ORDER BY price desc" );
		if( $result ){
			$charging_CLI = $result['chargingcli'];
			$validity = $result['validity'];
			$message = RESPONSE_SUBSCRIPTION_SUCESSFULL;
			$search = array ("~keyword~", "~cost~", "~period~","~unsubkey~","~mocli~");
			$replace = array ($serviceName , $result['price'], $validity, $unsubKey, $mocli);
			$message = str_replace ( $search, $replace, $message );
			$returnVal = array( "amount"=>$result['price'] , "cli"=>$charging_CLI , "validity"=>$validity, "MTmessage"=>$message );	
			
			// Charging Process for subscription
			
			/*  XML generation code start */			
				
				
				
				$varXMLCode = '<?xml version="1.0" encoding="UTF-8"?>
									<Message><Request Type="OBSRequest" RefNo="'.$varSystemRandomNo .'">
									<UserID>'.INTEGRAT_USERNAME.'</UserID>
									<Password>'.INTEGRAT_PASSWORD.'</Password>
									<OBSRequest Validity="00020000" Flags="32">
									<Ticket Type="Mobile"
												  OBSService=""
												  Service="C5T01"					
												  SubService=""
												  ChargeAddr="'.$varMSISDN.'"			
												  Description="TEST PRODUCT"		
												  Value="'.$varChargeAmount.'"					
												  Currency="'.INTEGRAT_CURRENCY.'"/>
									<Subscr Started="'.$varStartDate.' ' .$varSubStartStop.'" 	
												  Category="Ringtones"   
												  Trigger=""/>
									</OBSRequest></Request></Message>';
				echo $varXMLCode;
				
				

				/*XML generation code end */
				
				
				//$integrat_subscription_post = "http://xhg-lb1.higate.co.za:8888/hg_request/"
				// post xml to this request URL					
					
					//CURL
					
					echo "CURL INIT START - Subscription";
				
					$c = curl_init();
					curl_setopt($c, CURLOPT_URL,INTEGRAT_SUBSCRIPTION_NOTIFICATION_URL);
					curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($c, CURLOPT_POSTFIELDS, $varXMLCode );
					curl_setopt($c, CURLOPT_HTTPHEADER, array('Content-Type: application/xml'));
					
					$result = curl_exec($c);
					echo "CURL Execution End - Subscription";
					print_r($result);
					console ( LOG_LEVEL_TRACE, '('.$servicetype.') Subscription notification ---> XMLOutPut:'.$varXMLCode.' || Response ( '.$result);	
			
			
			
			
		}else{
			console ( LOG_LEVEL_WARN, " Charging amount details was not found for keywordid = ".$varKeyWordId );
		}
		
		return $returnVal;	
	
	}

	
	function pushContent($varMSISDN , $message , $cli)	
	{	//Pushing messages to the user
		global $restRequest, $db,$varSystemRandomNo;
		$db->query("INSERT INTO contentq (msisdn,cli,content,msgstatus,refid) VALUES ('".$varMSISDN."','".$cli."','". mysql_real_escape_string($message)."','0','".$varSystemRandomNo."')");
	}
	
	
	

?>