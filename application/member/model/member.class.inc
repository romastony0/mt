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
 * @filename:	member.class.inc
 * @filetype:	PHP
 * @filedesc:   This file contains the model for user module
 * 				and contains all the business logic for user module.
 *
 *
 */


class MemberModel {

	private $_insertValidataionRules;
	/**
	 * Description:
	 * constructor for user class will intialize the validition rules for insert operation.
	 *
	 * Parameters:
	 * null		: null
	 * Return Value:
	 * null		: null
	 */
	public function MemberModel() {
		$this->_insertValidataionRules = array (
			'username'	=> array (
				'type'		=> 'string',
				'required'	=> true,
				'striptags'	=> true,
				'length'	=> 255
			)
		);
	}

	public function validateUser() {
		global $db,$request;

		$query = "SELECT * FROM mtsoc_auth_users WHERE username='".$request['username']."' AND PASSWORD='".MD5($request['password'])."'";
		$result = $db['master']->getOneRow($query);
		if ($result && count($result) > 0)
			return $result;
		else
			return false;
	}
	
	public function get_user($user_id = '', $type = '') {
		global $db,$request;

		$query = "SELECT * FROM mtsoc_auth_users";
		if($user_id != '') {
			$query .= " WHERE id = '" . $user_id . "'";
			$result = $db['master']->getOneRow($query);
		} else if($type != '') {
			$query .= " WHERE type = '" . $type . "'";
			if($type == 'moderator')
				$result = $db['master']->getOneRow($query);
			else
				$result = $db['master']->getResults($query);
		} else {
			$result = $db['master']->getResults($query);
		}
		if ($result && count($result) > 0)
			return (object)$result;
		else
			return false;
	}
	
	public function get_user_stg($user_id, $type = '') {
		global $db,$request;

		$query = "SELECT * FROM mtsoc_users";
		if($user_id != '') {
			$query .= " WHERE id = '" . $user_id . "'";
			$result = $db['master']->getOneRow($query);
		} else if($type != '') {
			$query .= " WHERE type = '" . $type . "'";
			$result = $db['master']->getResults($query);
		} else {
			$result = $db['master']->getResults($query);
		}
		if ($result && count($result) > 0)
			return (object)$result;
		else
			return false;
	}

	public function create_user($args) {
		global $db,$request;
		
		$data = array(
			'username'			=>	(isset($args['username']) && !empty($args['username'])) ? $args['username'] : $args['first_name'],
			'email'				=>	$args['email'],
			'mobile_no'			=>	$args['mobile_no'],
			'first_name'		=>	$args['first_name'],
			'last_name'			=>	$args['last_name'],
			'country_id'		=>	$args['country_id'],
			'state_id'			=>	$args['state_id'],
			'university_id'		=>	$args['university_id'],
			'college_id'		=>	$args['college_id'],
			'registered_date'	=>	(isset($args['registered_on']) && !empty($args['registered_on'])) ? $args['registered_on'] : date('Y-m-d H:i:s'),
			'type'				=>	$args['type'],
			'branch_id'			=>	(isset($args['branch_id']) && !empty($args['branch_id'])) ? $args['branch_id'] : 0,
			'gender'			=>	$args['gender'],
			'modified_date' 	=>  date('y-m-d h:i:s')
		);
		if(($args['type'] == 'student' || $args['type'] == 'trial') && isset($args['id']) && !empty($args['id'])) {
			$data['id']			= $args['id'];
			$data['password']	= md5('test');
			$query = "INSERT INTO mtsoc_users (id, username, password, email, mobile_no, first_name, last_name, country_id, state_id, university_id, college_id, registered_date, type, branch_id, gender) VALUES ('". $data['id'] ."', '". $data['username'] ."', '". $data['password'] ."', '". $data['email'] ."', '". $data['mobile_no'] ."', '". $data['first_name'] ."', '". $data['last_name'] ."', '". $data['country_id'] ."', '". $data['state_id'] ."', '". $data['university_id'] ."', '". $data['college_id'] ."', '". $data['registered_date'] ."', '". $data['type'] ."', '". $data['branch_id'] ."', '". $data['gender'] ."')";
			return $db['master']->query($query);
		} elseif ( $args['type'] == 'SME' ) {
			$data['password']	= md5($args['password']);
			$query = "INSERT INTO mtsoc_auth_users (username, password, email, mobile_no, first_name, last_name, country_id, state_id, university_id, college_id, registered_date, type, branch_id, gender, modified_date, avatar) VALUES ('". $data['email'] ."', '". $data['password'] ."', '". $data['email'] ."', '". $data['mobile_no'] ."', '". $data['first_name'] ."', '". $data['last_name'] ."', '". $data['country_id'] ."', 0, '". $data['university_id'] ."', '". $data['college_id'] ."', '". $data['registered_date'] ."', '". $data['type'] ."', '". $data['branch_id'] ."', '". $data['gender'] ."', '". $data['modified_date'] ."', '')";
			return $db['master']->query($query);
		} else {
			return false;
		}
	}
	
	public function check_user($email, $mobile) {
		global $db,$request;
		
		$email = $db['master']->getOneRow("SELECT COUNT(*) AS count FROM mtsoc_auth_users WHERE email = '" . $email . "'");
		
		$mobile = $db['master']->getOneRow("SELECT COUNT(*) AS count FROM mtsoc_auth_users WHERE mobile_no = '" . $mobile . "'");
		//echo $db['master']->getOneRow($email_query)['count']; die;
		if($email['count'] > 0) {
			return 'email';
		} elseif($mobile['count'] > 0) {
			return 'mobile';
		} else {
			return true;
		}
		
	}
	
	public function get_country($id = '', $code = '') {
		global $db,$request;

		$query = "SELECT * FROM mtsoc_countries";
		if(!empty($id))
			$query .= " WHERE id = '$id'";
		if(!empty($code))
			$query .= " WHERE code = '$code'";
		
		$position	= strpos($query, 'WHERE');
		if ($position !== false) {
			$query = substr($query, 0, $position + 1) . str_replace('WHERE', 'AND', substr($query, $position + 1));
		}
		$query .= " ORDER BY name";
		if(!empty($id) || !empty($code))
			$result = $db['master']->getOneRow($query);
		else
			$result = $db['master']->getResults($query);
		if ($result && count($result) > 0)
			return (object)$result;
		else
			return false;
	}
	
	public function get_state($id = '', $country_id = '') {
		global $db,$request;

		$query = "SELECT * FROM mtsoc_states";
		if(!empty($id))
			$query .= " WHERE id = '$id'";
		if(!empty($country_id))
			$query .= " WHERE country_id = '$country_id'";
		
		$position	= strpos($query, 'WHERE');
		if ($position !== false) {
			$query = substr($query, 0, $position + 1) . str_replace('WHERE', 'AND', substr($query, $position + 1));
		}
		$query .= " ORDER BY name";
		if(!empty($id))
			$result = $db['master']->getOneRow($query);
		else
			$result = $db['master']->getResults($query);
		
		if ($result && count($result) > 0)
			return (object)$result;
		else
			return false;
	}
	
	public function get_university($id = '', $state_id = '') {
		global $db,$request;

		$query = "SELECT * FROM mtsoc_universities";
		if(!empty($id))
			$query .= " WHERE id = '$id'";
		if(!empty($state_id))
			$query .= " WHERE country_id = '$state_id'";
		
		$position	= strpos($query, 'WHERE');
		if ($position !== false) {
			$query = substr($query, 0, $position + 1) . str_replace('WHERE', 'AND', substr($query, $position + 1));
		}
		$query .= " ORDER BY name";
		if(!empty($id))
			$result = $db['master']->getOneRow($query);
		else
			$result = $db['master']->getResults($query);
		
		if ($result && count($result) > 0)
			return (object)$result;
		else
			return false;
	}
	
	public function get_college($id = '', $university_id = '') {
		global $db,$request;

		$query = "SELECT * FROM mtsoc_colleges";
		if(!empty($id))
			$query .= " WHERE id = '$id'";
		if(!empty($university_id))
			$query .= " WHERE university_id = '$university_id'";
		
		$position	= strpos($query, 'WHERE');
		if ($position !== false) {
			$query = substr($query, 0, $position + 1) . str_replace('WHERE', 'AND', substr($query, $position + 1));
		}
		$query .= " ORDER BY name";
		if(!empty($id))
			$result = $db['master']->getOneRow($query);
		else
			$result = $db['master']->getResults($query);
		
		if ($result && count($result) > 0)
			return (object)$result;
		else
			return false;
	}
	
	public function get_branch($id = '', $code = '') {
		global $db,$request;

		$query = "SELECT * FROM mtsoc_branch";
		if(!empty($id))
			$query .= " WHERE branch_id = '$id'";
		if(!empty($code))
			$query .= " WHERE branch_code = '$code'";
		
		$position	= strpos($query, 'WHERE');
		if ($position !== false) {
			$query = substr($query, 0, $position + 1) . str_replace('WHERE', 'AND', substr($query, $position + 1));
		}
		$query .= " ORDER BY branch_id";
		if(!empty($id) || !empty($code))
			$result = $db['master']->getOneRow($query);
		else
			$result = $db['master']->getResults($query);
		if ($result && count($result) > 0)
			return (object)$result;
		else
			return false;
	}

	public function check_password($password = '', $user_id = '') {
		global $db,$request;

		$query = "Select password from mtsoc_auth_users where id=".$user_id;
		$result = $db['master']->getOneRow($query);
		
		if ($result && count($result) > 0)
			return $result['password'];
		else
			return false;
	}

	public function change_password($args = '') {
		global $db,$request;
		
		$query = "UPDATE mtsoc_auth_users SET password = '".md5($args['new_password'])."' WHERE id =".$args['user_id'];
		$result = $db['master']->getResults($query);
		
		if ($result && count($result) > 0)
			return true;
		else{
			echo false;
		}
	}
}
?>
