<?php
	include(ROOT_DIR.'application/member/member.init.php');
	include(ROOT_DIR.'application/qa/model/qa.class.inc');
	
	function mtutor_current_user() {
		$member_model = new MemberModel();
		if(isset($_SESSION['userid']) && $_SESSION['userid'] != '') {
			return $member_model->get_user($_SESSION['userid']);
		} else {
			return false;
		}
	}
	
	function mtutor_get_body_class() {
		global $request, $tsResponse, $library;
		$return = '';
		$user = mtutor_current_user();
		if(isset($user) && !empty($user)) {
			if(isset($request)) {
				if($request['application'] == 'member' && $request['action'] == 'initiate') {
					$return = 'page-members subpage-index';
				} else if($request['application'] == 'qa' && $request['action'] == 'dashboard') {
					$return = 'page-qa subpage-index';
				} else if($request['application'] == 'qa' && $request['action'] == 'chatroom') {
					$return = 'page-ChatRoom subpage-index';
				} else if($request['application'] == 'qa' && $request['action'] == 'similar_questions') {
					$return = 'page-qa subpage-similar_question';
				} else if($request['application'] == 'qa' && $request['action'] == 'answer') {
					$return = 'page-qa subpage-edit';
				} else if($request['application'] == 'qa' && $request['action'] == 'rejected_questions') {
					$return = 'page-qa subpage-rejected_list';
				} else {
					$return = '';
				}
			}
			if($user->type == 'MODERATOR') {
				$return .= ' user-role-moderator';
			} else if($user->type == 'SME') {
				$return .= ' user-role-faculty';
			}
		}
		return $return;
	}
	
	function mtutor_is_loggedin() {
		if( isset($_SESSION['userid']) && !empty($_SESSION['userid']) ) {
			return true;
		} else {
			return false;
		}
	}
	
	function mtutor_excerpt_length($string, $limit=100, $ending = '&hellip;') {
		if(strlen($string) > $limit) {
			return substr($string, 0, $limit) . $ending;
		}
		return $string;
	}
/*
// This function is used for internal testing purpose and disable this function during real time integration
	function send_answer( $question_id, $answer, $attachment ) {
		if( !empty($question_id) && !empty($answer) ) {
			if($attachment	== 'rejected') {
				$post	= $answer;
			} else {
				$qa_model	= new QaModel();
				$answer_row = (object)$qa_model->get_answer_by_qid($question_id);
				$post		= $answer_row;
			}
			$URL		= "http://192.168.64.29/socialqc/answer_insert.php";
			$ch			= curl_init();
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
			curl_setopt($ch, CURLOPT_URL, $URL);			
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$response	= curl_exec($ch);
			echo $response;
			if(curl_error($ch)) {
				return false;
			} else {
				curl_close($ch);
				return $response;
			}
		} else {
			return false;
		}
	}
*/
 // Enable this function during real time integration 
	function send_answer( $question_id, $answer, $attachment ) {
		if( !empty($question_id) && !empty($answer) ) {
			if(!empty($attachment)) {
				if($attachment != 'rejected') {
					$attachment = APPLICATION_URL.'storage/uploads/answer_attachment/'.$attachment;
				} else {
					$attachment = '';
					$answer		= $answer['answer_content'];
				}
			} else {
				$attachment = '';
			}
			$post = array(
				'id'			=> $question_id,
				'reply'			=> $answer,
				'attachment'	=> $attachment,
				'oauth'			=> 'e8a1d06ef109caaa7876cf770acbc2c9',
				'type'			=> 'askdoubtreply'
			);
			$url	= 'http://192.168.100.59/mtutor/gateway/mtutorAPI_5.php';
			$ch		= curl_init();
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
			curl_setopt($ch, CURLOPT_URL, $url);			
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$response = curl_exec($ch);
			if(curl_error($ch)) {
				return false;
			} else {
				curl_close($ch);
				return $response;
			}
		} else {
			return false;
		}
	}

	function send_email_sms( $type, $args ){
		$member_model	= new MemberModel();
		$qa_model		= new QaModel();
		
		$moderator		= $member_model->get_user( '', 'moderator' );
		if($type == 'assigned'){
			$faculty	= $member_model->get_user( $args['faculty_id'], '' );
			$question 	= $qa_model->post_details_by_qid( $args['question_id'], '' );
			$from_email	= $moderator->email;
			$from_name	= "Moderator - MTutor";
			$to_email	= $faculty->email;
			$subject	= "New Question Assigned - " . date('d-m-Y');
			$content	= 'You have been assigned a new question.<br><br>
				Question Title : '. $question->question_title;

			$mobile			= $faculty->mobile_no;
			$mobile_message	= "New question assigned to you at " . date('d-m-Y H:i');
		}elseif($type == 'answered'){
			$faculty		= $member_model->get_user( $args['faculty_id'], '' );
			$question 		= $qa_model->post_details_by_qid( $args['question_id'], '' );
			$from_email		= $faculty->email;
			$from_name 		= $faculty->first_name . " - MTutor";
			$to_email 		= $moderator->email;
			$subject		= "Question is Answered - " . date('d-m-Y');
			$content		= 'Assigned question has been answered.<br><br>
				Question Title : '. $question->question_title;

			$mobile			= $moderator->mobile_no;
			$mobile_message	= "Assigned question has been answered at " . date('d-m-Y H:i') . ' - ' . $faculty->first_name;
		}elseif($type == 'respond_in'){
			$faculty		= $member_model->get_user( $args['faculty_id'], '' );
			$question 		= $qa_model->post_details_by_qid( $args['question_id'], '' );
			$from_email		= $faculty->email;
			$from_name 		= $faculty->first_name . " - MTutor";
			$to_email 		= $moderator->email;
			$subject		= "Question is Responded - " . date('d-m-Y');
			$content		= 'Assigned question will be responded in - ' . $args['respond_time'] . ' Min - '.$faculty->first_name.'.<br><br>
				Question Title : '. $question->question_title;

			$mobile			= $moderator->mobile_no;
			$mobile_message	= "Assigned question will be responded in - " . $args['respond_time'] . ' Min - ' . $faculty->first_name;
		}elseif($type == 'approved'){
			$question 	= $qa_model->post_details_by_qid( $args['question_id'], '' );
			$student	= $member_model->get_user_stg( $question->author_id, '' );
			$from_email	= $moderator->email;
			$from_name	= $moderator->first_name . " - MTutor";
			$to_email	= $student->email;
			$subject	= "Answer Posted - " . date('d-m-Y');
			$content	= 'Answer is posted for your Question.<br><br>
				Question Title : '. $question->question_title;

			$mobile			= $student->mobile_no;
			$mobile_message	= "Answer is posted for your question. - Mobile Tutor";
		}elseif($type == 'question_rejected'){
			$question 	= $qa_model->post_details_by_qid( $args['question_id'], '' );
			$student	= $member_model->get_user_stg( $question->author_id, '' );
			$from_email	= $moderator->email;
			$from_name	= $moderator->first_name . " - MTutor";
			$to_email	= $student->email;
			$subject	= "Question Rejected - " . date('d-m-Y');
			$content	= 'Your question is rejected.<br><br>
				Question Title : '. $question->question_title . '<br><br>
				Rejected Reason : ' . $args['reason'];

			$mobile			= $student->mobile_no;
			$mobile_message	= "Your question is rejected. - Mobile Tutor";
		}elseif($type == 'answer_rejected'){
			$question			= $qa_model->post_details_by_qid( $args['question_id'], '' );
			$rejected_answer	= $qa_model->get_rejected_answer( $args['answer_id'] );
			$faculty			= $member_model->get_user( $rejected_answer->author_id, '' );
			$from_email 		= $moderator->email;
			$from_name 			= $moderator->first_name . " - MTutor";
			$to_email 			= $faculty->email;
			$subject		 	= "Answer Rejected - " . date('d-m-Y');
			$content		 	= 'Your answer is rejected by moderator.<br><br>
				Question Title : '. $question->question_title . '<br><br>
				Rejected Answer : ' . $rejected_answer->answer_content . '<br><br>
				Rejected Reason : ' . $args['reason'];

			$mobile			= $faculty->mobile_no;
			$mobile_message	= "Your answer is rejected by moderator. - Mobile Tutor";
		}
		$email_args	= array(
			'from_email'	=> $from_email,
			'from_name'		=> $from_name,
			'to_email'		=> $to_email,
			'subject'		=> $subject,
			'content'		=> $content
		);
		$sms_args	= array(
			'number'	=> $mobile,
			'message'	=> $mobile_message
		);
		send_email( $email_args );
		send_sms( $sms_args );
		return $email_args;
	}

	function send_sms( $args = array() ) {
		if( isset($args['number']) && !empty($args['number']) && isset($args['message']) && !empty($args['message']) ) {
			$mobile_number	= format_number($args['number']);
			$oauth			= 'e8975be38def57f45ba3736d859dec62';
			$type			= 'sendSMS';
			$post_fields	= "msisdn=".$mobile_number."&msg=".$args['message']."&oauth=".$oauth."&type=".$type."";
			
			$url	= 'http://online.m-tutor.com/mtutor/gateway/mtutorAPI_1.php';
			$ch		= curl_init();
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
			curl_setopt($ch, CURLOPT_URL, $url);			
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$response = curl_exec($ch);
			if(curl_error($ch)) {
				return false;
			} else {
				curl_close($ch);
				return $response;
			}
			
			/*$ch = curl_init("http://online.m-tutor.com/mtutor/gateway/mtutorAPI_1.php");
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			$output = curl_exec($ch);
			$error = curl_error($ch);
			if( !empty( $error ) ) {
				curl_close($ch);
				return $output;
			} else {
				return false;
			}*/
		}else{
			return false;
		}
	}

	function format_number($number) {
		if( is_numeric($number) ) {
			if( strlen($number) >= 10 && strlen($number) <= 13 ) {
				return '+91' . substr($number, -10);
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	function send_email( $args = array() ) {
		if( isset($args['from_email']) && !empty($args['from_email'])  && isset($args['from_name']) && !empty($args['from_name']) && isset($args['to_email']) && !empty($args['to_email']) && isset($args['subject']) && !empty($args['subject']) && isset($args['content']) && !empty($args['content']) ) {
			$type			= 'sendEmail';
			$oauth			= 'e8975be38def57f45ba3736d859dec62';
			$post_fields	= 	"toemail=".$args['to_email'].
								"&fromemail=".$args['from_email'].
								"&fromname=".$args['from_name'].
								"&subject=".$args['subject'].
								"&content=".urlencode($args['content']).
								"&oauth=".$oauth.
								"&type=".$type;
			$ch = curl_init("http://online.m-tutor.com/mtutor/gateway/mtutorAPI_1.php");
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS,$post_fields);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			$output = curl_exec($ch);
			$error = curl_error($ch);
			if( !empty( $error ) ) {
				curl_close($ch);
				return $output;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	
	function encrypt_decrypt($action, $string) {
		$output = false;
		$encrypt_method	= "AES-256-CBC";
		$secret_key		= 'mhmdhbfj^%$%^$vvjd';
		$secret_iv		= 'hddgafjhdabjh&^%';
		// hash
		$key = hash('sha256', $secret_key);
		// iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
		$iv = substr(hash('sha256', $secret_iv), 0, 16);
		if ( $action == 'encrypt' ) {
			$output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
			$output = base64_encode($output);
		} else if( $action == 'decrypt' ) {
			$output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
		}
		return $output;
	}
?>