<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/**
 * Testing Model
 */

class Test_model extends CI_Model {

	var $Table_name_user = 'user';
	var $KEY_first_name = 'firstname';
	var $KEY_last_name = 'lastname';

	
	
	function get_all_user() {
	    $result= $this->db->get($this->Table_name_user);
	    //echo 'db conn';
	    return $result->result_array();
	}

}

/* End of file test_model.php */
/* Location: ./application/models/test_model.php */