<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/**
 * User Model
 * @author Jason Ng
 */

class User_model extends CI_Model {
	var $Table_name_user = 'User';
	
	var $KEY_user_id = 'uid';
	var $KEY_email = 'email';
	var $KEY_user_name = 'username';
	var $KEY_password = 'password';
	
	//var $KEY_profile_pic = 'profile_pic';
	var $KEY_status = 'status';
	var $KEY_fb_access_token='fb_access_token';
	

	function add_user($data) {
		$this->db->insert($this->Table_name_user, $data);	
		if ($this->db->affected_rows() > 0) {
			return $this->db->insert_id();
		} else {
			return -1;
		}
	}
	
	function check_if_user_exists($key, $value) {
		$number_of_result = $this->db->from($this->Table_name_user)
							->where($key, $value)
							->count_all_results();
		if ($number_of_result > 0) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
	
	function get_user_by_email($email) {
		return $this->get_user_by_key($this->KEY_email, $email);
	}

	function get_user_by_id($id) {
		return $this->get_user_by_key($this->KEY_user_id, $id);
	}
	
	// helper
	private function get_user_by_key($key, $value) {
		$result = $this->db->from($this->Table_name_user)
							->where($key, $value)
							->get();

		if ($result->num_rows() > 0) {
			return $result->row_array(1);
		} else {
			return array();
		}

	}

	//fb related
	function update_fb_access_token($uid,$fb_access_token){
	  $data = array(
		  	$this->KEY_fb_access_token=>$fb_access_token,
	  );
	  $this->db->where($this->KEY_user_id,$uid);
      $this->db->update($this->Table_name_user, $data); 
    
      if($this->db->affected_rows() > 0)
        return true ;
      else
        return false;
	}
	
	
}

/* End of file user_model.php */
/* Location: ./application/models/user_model.php */