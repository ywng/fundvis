<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Check and generate Session data for users
*
 * @author jason ng
 */
class Session_model extends CI_Model{

    var $KEY_id = 'id';
    var $KEY_session_token = 'session_token';
    var $KEY_expire_time = 'expire_time';
    var $KEY_user_type = 'user_type';
    var $Table_Name = 'Session';
    

    function __construct() {
        parent::__construct();
    }
    
    
    /*
     * 
     */
    
    function session_token_based_on_id($id, $user_type) {
        $check_expiration = $this->is_session_token_expired($id, $user_type);
        if ($check_expiration == 1) {
            // expired
            return array(
                'session_token' => $this->get_session_token_based_on_id($id, $user_type),
                'expired' => 1,
            );
        } else if ($check_expiration == 0) {
            return array(
                'session_token' => $this->get_session_token_based_on_id($id, $user_type),
                'expired' => 0,
            );
        } else {
            return array();
        }
        
    }

    function get_session_by_id($id, $user_type) {
        $result = $this->db->select($this->KEY_session_token . ', ' . $this->KEY_expire_time)
                ->where($this->KEY_id, $id)
                ->where($this->KEY_user_type, $user_type)
                ->from($this->Table_Name)
                ->get();
        if ($result->num_rows() > 0) {
            return $result->row_array('1');
        } else {
            return array();
        }
    }

    /**
     * assumption there exists the record already
     * @return string a valid session token
     */
    
    private function get_session_token_based_on_id($id, $user_type) {
        $result = $this->db->select($this->KEY_session_token . ', ' . $this->KEY_expire_time)
                ->where($this->KEY_id, $id)
                ->where($this->KEY_user_type, $user_type)
                ->from($this->Table_Name)
                ->get();
        $row = $result->row_array('1');
        return $row[$this->KEY_session_token];
    }


    /**
     * 
     * @param int $id
     * @param enum $user_type
     * @return string a generated session token for the specific user
     */
    function generate_new_session_token($id, $user_type) {

		// $bytes = openssl_random_pseudo_bytes(128, $cstrong);

		// $session_token = bin2hex($bytes);

        $mtrand = mt_rand(); // generate a random number for session computing

        $session_token = hash('sha512', time().$mtrand, FALSE);
		
		$expired = $this->is_session_token_expired($id, $user_type);
		
		if ($expired == -1) {
			$this->insert_session_token($id, $user_type, $session_token);
		} else {
			$this->update_session_token($id, $user_type, $session_token);
		}

        return (string)$session_token;
    }
    

    function expire_session($id, $user_type) {
        $now = time();
        $this->db->set($this->KEY_expire_time, date('Y-m-d G:i:s', $now))
                ->where($this->KEY_id, $id)
                ->where($this->KEY_user_type, $user_type)
                ->update($this->Table_Name);
        if ($this->db->affected_rows() > 0) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     *
     * @param int $id
     * @param string $user_type
     * @return int 1 if it is expired, 0 if it is not expired, -1 if no record
     */

    private function is_session_token_expired($id, $user_type) {
        $result = $this->db->select($this->KEY_expire_time)
                ->where($this->KEY_id, $id)
                ->where($this->KEY_user_type, $user_type)
                ->from($this->Table_Name)
                ->get();

        if ($result->num_rows() > 0) {
            $row = $result->row_array('1');
            if (time() - strtotime($row[$this->KEY_expire_time]) >= 0) {
                // expired
                return 1;
            } else {
                return 0;
            }
        } else {
            return -1;
        }

    }




    private function insert_session_token($id, $user_type, $session_token) {
		$nextDay = time() + (24 * 60 * 60 * 7);	//7 days session valid time
		$this->db->set($this->KEY_session_token, $session_token)
				->set($this->KEY_id, $id)
                ->set($this->KEY_user_type, $user_type)
				->set($this->KEY_expire_time, date('Y-m-d G:i:s', $nextDay))
				->insert($this->Table_Name);

		if ($this->db->affected_rows() > 0) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

    private function update_session_token($id, $user_type, $session_token) {
		$nextDay = time() + (24 * 60 * 60 * 7);
		$this->db->set($this->KEY_session_token, $session_token)
				->set($this->KEY_expire_time, date('Y-m-d G:i:s', $nextDay))
				->where($this->KEY_id, $id)
                ->where($this->KEY_user_type, $user_type)
				->update($this->Table_Name);
		
		if ($this->db->affected_rows() > 0) {
			return TRUE;
		} else {
			return FALSE;
		}

    }
}

/* end of file session_model.php */