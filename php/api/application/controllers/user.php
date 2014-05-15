<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/**
 * User
 */

require_once (APPPATH. 'libraries/REST_Controller.php');

class User extends REST_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->library('CORE_Controller');
		$this->load->helper(array('form', 'url'));
		$this->core_controller->set_response_helper($this);
		$CI = & get_instance();
		$CI->config->load("facebook",TRUE);
		$config = $CI->config->item('facebook');
		$this->load->library('Facebook', $config);
	
	}

	var $user_type = '';

	public function index()
	{
		
	}
	
	// (DEBUG: this function is for testing purpose only, to be removed)
	public function test_function_post(){
		$this->load->model('user_model'); 
		$result = $this->user_model->check_if_user_exists($this->user_model->KEY_email,$this->input->post('email'));
		$this->core_controller->add_return_data('result', $result); 
		$this->core_controller->successfully_processed();
	}
	
	// (DEBUG: not yet test)
	// input: email, firstname, lastname, password
	public function register_post()
	{
		// (TODO) Validation
	
        $this->load->model('user_model');

        $existance = $this->user_model->check_if_user_exists($this->user_model->KEY_email,$this->input->post('email'));
        if ($existance) {
            $this->core_controller->fail_response(10);
        }

        $data = array(
                $this->user_model->KEY_first_name => $this->input->post('firstname'),
                $this->user_model->KEY_last_name => $this->input->post('lastname'),
                $this->user_model->KEY_password => $this->input->post('password'),
                $this->user_model->KEY_email => $this->input->post('email')
        );
        $user_id = $this->user_model->add_user($data);
        if ($user_id < 0) {
                $this->core_controller->fail_response(11);
        }
        $this->core_controller->add_return_data('user_id',$user_id);
		$this->core_controller->successfully_processed();
	}

	public function login_post()
	{
		// (TODO) Validation
	
        $this->load->model('user_model');
		
		$user_data = $this->user_model->get_user_by_email($this->input->post('email'));
		if (count($user_data) == 0) {
			// email does not exist
			$this->core_controller->fail_response(3);
		}
		if ($user_data[$this->user_model->KEY_password] != $this->input->post('password')) {
			// wrong password
			$this->core_controller->fail_response(4);
		}
		
		$new_session_token = $this->get_valid_session_token_for_user($user_data[$this->user_model->KEY_user_id]);

		foreach ($this->hide_user_data($user_data) as $key => $value) {
			$this->core_controller->add_return_data($key, $value);
		}

		$this->core_controller->add_return_data('session_token', $new_session_token['session_token']);
							//->add_return_data('expire_time', $new_session_token['expire_time']);
		
		$this->core_controller->successfully_processed();
	}

	public function fblogin_post()
	{
		$this->load->model('user_model');
	
        // Try to get the user's id on Facebook
        $accessToken_fb=$this->input->post('access_token');
        $this->facebook->setAccessToken($accessToken_fb);
        $userfbId = $this->facebook->getUser();
 
        // If user is not yet authenticated, the id will be zero
        if($userfbId == 0){
            // invalid access token, return with error
             $data['url'] = $this->facebook->getLoginUrl(array('scope'=>'email'));
             $this->core_controller->add_return_data('login_url', $data['url']); 
             $this->core_controller->fail_response(5);
  
        } else {
            // Get user's data 
            $fb_user = $this->facebook->api('/me');
            $this->core_controller->add_return_data('user_fbid', $userfbId); 

            //if user is first time login with fb api
            //create an entry record in our user table
            $user_data = $this->user_model->get_user_by_email($fb_user['email']);
            
	        if (!$user_data) {
	            //create an entry record for future activity 
	             $data = array(
	                $this->user_model->KEY_first_name => $fb_user['first_name'],
	                $this->user_model->KEY_last_name =>  $fb_user['last_name'],
	                $this->user_model->KEY_email => $fb_user['email']
       			 );
       			 $user_id = $this->user_model->add_user($data);

		         if ($user_id < 0) {
		                $this->core_controller->fail_response(5);
		         }	
		
			    $new_session_token = $this->get_valid_session_token_for_user($user_id);
			    $user_data = $this->user_model->get_user_by_id($user_id);		
	        }

	        foreach ($this->hide_user_data($user_data) as $key => $value) {
				$this->core_controller->add_return_data($key, $value);
			}

	        $new_session_token = $this->get_valid_session_token_for_user($user_data[$this->user_model->KEY_user_id]);
	        $this->core_controller->add_return_data('session_token', $new_session_token['session_token']);
							//->add_return_data('expire_time', $new_session_token['expire_time']);
            $this->core_controller->successfully_processed();
            
        }
		
		
	}

	/* helper function */
	private function get_valid_session_token_for_user($id) {
		$this->load->model('session_model');
		$result = $this->session_model->session_token_based_on_id($id, $this->user_type);
        if (!is_null($result) && is_array($result) && count($result) > 0) {
            // has session token, check
            
            //Not implement session_expire::
            //At the moment, we do not implement session expire time, it is supposed to be forever
            //so we just ignore the expire check to accomplish it
            //if we need it again in the future, just uncomment it again
       		//if (!$result['expired']) {
		    	return $this->session_model->get_session_by_id($id, $this->user_type);
		    //}
        }
        $this->session_model->generate_new_session_token($id, $this->user_type);
        return $this->session_model->get_session_by_id($id, $this->user_type);
	}
	
	
	private function hide_user_data($user_data_array) {
		$this->load->model('user_model');
		if (array_key_exists($this->user_model->KEY_password, $user_data_array)) {
			unset($user_data_array[$this->user_model->KEY_password]);
		}

		return $user_data_array;
	}

	

}

/* End of file user.php */
/* Location: ./application/controllers/user.php */