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
 * @filename:	member.service.class.inc
 * @filetype:	PHP
 * @filedesc:   This file contains the controller for members Module
 * 				which select the action or state for model depends
 * 				upon the user input given to the controller.
 * 
 *
 */

session_start();
class MemberController{
	/**
	 * Description:
	 * This is controller for user Module,it will decide the action  to be given to 
	 * the model class depends upon the action parameter given to the
	 * controller.
	 * 
	 * Parameters:
	 *	null		: null
	 * Return Value: 
	 *	null		: null
	*/
	function MemberController(){
		global $request, $tsResponse, $library;
		//Collecting the action parameter from request.
		
		$action = strtolower ( trim ( $request ['action'] ) );
		switch ($action) {
			case "initiate" :
				if(isset($_SESSION['userid']) && !empty($_SESSION['userid'])) {
					include(ROOT_DIR.'gateway/header.php');
					include(ROOT_DIR.'gateway/footer.php');
				} else {
					header('location: '.APPLICATION_URL);
				}
				break;
			case "login_form" :
				if(isset($_SESSION['userid']) && !empty($_SESSION['userid'])) {
					header('location: '.APPLICATION_URL.'gateway/action?application=member&action=initiate');
				} else {
					include(ROOT_DIR.'gateway/login.php');
				}
				break;
			case "login" :
				if(isset($_SESSION['userid']) && !empty($_SESSION['userid'])) {
					header('location: '.APPLICATION_URL.'gateway/action?application=member&action=initiate');
				} else {
					$memberModelObj = new MemberModel();
					$member			= $memberModelObj->validateUser();
					if ($member != false) {
						$_SESSION['userid']		= $member['id'];
						$_SESSION['usertype']	= $member['type'];
						$_SESSION['username']	= $member['first_name'].' '.$member['last_name'];
						header('location: '.APPLICATION_URL.'gateway/action?application=member&action=initiate');
					} else {
						header('location: '.APPLICATION_URL.'gateway/action?application=member&action=login_form&display=error');
					}
				}
				break;
			case "register" :
				$user	= mtutor_current_user();
				if(!mtutor_is_loggedin() || $user->type != 'MODERATOR') {
					header('location: '.APPLICATION_URL);
				}
				include(ROOT_DIR.'gateway/header.php');
				$memberModel	= new MemberModel();
				$countries		= $memberModel->get_country();
				$branches		= $memberModel->get_branch();
				include(ROOT_DIR.'gateway/register.php');
				include(ROOT_DIR.'gateway/footer.php');
				break;
			case "logout" :
				session_destroy();
				header('location: '.APPLICATION_URL);
				break;
			case "change_password" :
				$user	= mtutor_current_user();
				if(!mtutor_is_loggedin()) {
					header('location: '.APPLICATION_URL);
				}
				include(ROOT_DIR.'gateway/header.php');
				include(ROOT_DIR.'gateway/change_password.php');
				include(ROOT_DIR.'gateway/footer.php');
				break;
			case "change_password_submit" :
				$validate = $this->change_password_validate($request);
				$memberModel	= new MemberModel();
				if($validate['status'] != false) {
					$check_password	= $memberModel->check_password($request['current_password'], $request['user_id']);
					
					//if(!(password_verify($request['current_password'], $check_password))){
					if(md5($request['current_password']) !=  $check_password){
						echo json_encode(array('status'=> false, 'key' => 'Please Give Correct Current Password.', 'type' => 'custom'));
					} else if(strlen($request['new_password']) < 4 || strlen($request['new_password']) > 16) {
						echo json_encode(array('status'=> false, 'key' => 'Password lenth should be 4 to 16.', 'type' => 'custom'));
					} else if($request['new_password'] != $request['confirm_password']) {
						echo json_encode(array('status'=> false, 'key' => 'Password Mismatched.', 'type' => 'custom'));
					} else {				
						$args	= $request;
						$memberModel->change_password($args);
						echo json_encode(array('status'=> true, 'message' => 'Success'));
					}
				} else {
					echo json_encode(array('status'=> false, 'key' => $validate['key'], 'type' => $validate['type']));
				}
				break;
		}
	}

//change_password validate
	function change_password_validate($field) {
		$return = array('status' => false, 'type' => 'text');
		if(empty($field['current_password'])) {
			$return['key'] = 'Current Password';
		} elseif(empty($field['new_password'])) {
			$return['key'] = 'New Password';
		} elseif(empty($field['confirm_password'])) {
			$return['key'] = 'Confirm Password';
		} else {
			$return['status'] = true;
		}
		return $return;
	}
	
}
?>