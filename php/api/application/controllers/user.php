<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/**
 * @author Jason Ng
 */

require_once (APPPATH. 'libraries/REST_Controller.php');

class User extends REST_Controller {

	var $user_type = 'user';

	public function __construct() {
		parent::__construct();
		$this->load->library('CORE_Controller');
		$this->load->helper(array('form', 'url'));
		$this->core_controller->set_response_helper($this);
		$CI = & get_instance();
		$CI->config->load("facebook",TRUE);
		$config = $CI->config->item('facebook');
		$this->load->library('Facebook', $config);

		$this->load->model('user_model');
	
	}

	

	public function index()
	{
		
	}
	
	/**
	*  INPUT: email, firstname, lastname, password
	*  Summary: Register + login together
	*/
	public function register_post()
	{
		/*// Validation
		$this->load->library('form_validation');
		$validation_config = array(
			array('field' => 'password', 'label' => 'password', 'rules' => 'trim|required|xss_clean|md5'), 
			array('field' => 'email', 'label' => 'email', 'rules' => 'trim|required|xss_clean'), 
			array('field' => 'username', 'label' => 'user name', 'rules' => 'trim|xss_clean'), 
			//array('field' => 'firstname', 'label' => 'firstname', 'rules' => 'trim|required|xss_clean'), 
			//array('field' => 'lastname', 'label' => 'lastname', 'rules' => 'trim|required|xss_clean'), 
		);
		$this->form_validation->set_error_delimiters('<error>', '')->set_rules($validation_config);
		if ($this->form_validation->run() === FALSE) {
			$this->core_controller->fail_response(2, validation_errors() );	
		}
	
		// Register
        $this->load->model('user_model');

        $existance = $this->user_model->check_if_user_exists($this->user_model->KEY_email,$this->input->post('email'));
        if ($existance) {
            $this->core_controller->fail_response(10);
        }

        $data = array(
                //$this->user_model->KEY_first_name => $this->input->post('firstname'),
               // $this->user_model->KEY_last_name => $this->input->post('lastname'),
        		$this->user_model->KEY_user_name => $this->input->post('username'),
                $this->user_model->KEY_password => $this->input->post('password'),
                $this->user_model->KEY_email => $this->input->post('email')
        );
        $user_id = $this->user_model->add_user($data);
        if ($user_id < 0) {
			$this->core_controller->fail_response(11);
        }
		
		// upload profile pic
        $config['upload_path'] = './uploads/profile_pic';	//TODO: where is the path
		//$config['allowed_types'] = 'JPEG|PNG';
		//$config['max_size']	= '100000';
		//$config['max_width']  = '10240';
		//$config['max_height']  = '887680';

		$this->load->library('upload', $config);
		$url = null;
		if ( ! $this->upload->do_upload('profilepic'))
		{
			$error = array('error' => $this->upload->display_errors());
			// var_dump($error);
			//$this->load->view('upload_form'¡A$error);
			 $this->core_controller->add_return_data('upload_image_error', $error);
			 $this->core_controller->fail_response(5);
		}
		else
		{
			$file_data =  $this->upload->data();

			//$this->load->view('upload_success'¡A$data);

			// prepare to upload to S3 first
			$this->load->helper('upload');
			$this->load->config('amazon');
			$accessKey = $this->config->item('amazonS3AccessKey');
			$secretKey = $this->config->item('amazonS3SecretKey');

			$url = upload_to_s3($file_data['full_path'], $file_data['file_name'], $accessKey, $secretKey);
			if (!$url) {
				$this->core_controller->add_return_data('upload_image_error', "Cannot upload to s3");
				$this->core_controller->fail_response(5);
			}

			$this->core_controller->add_return_data('image_data', $file_data);
		}
		
		
		// Login
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

		// Return JSON
		foreach ($this->hide_user_data($user_data) as $key => $value) {
			$this->core_controller->add_return_data($key, $value);
		}
		
		$this->core_controller->add_return_data('session_token', $new_session_token['session_token']);
		$this->core_controller->add_return_data('expire_time', $new_session_token['expire_time']);
		$this->core_controller->successfully_processed();
		
		*/
	}

	
	/**
	*  INPUT: email, password
	*/
	public function login_post()
	{
		// Validation
		$this->load->library('form_validation');
		$validation_config = array(
			array('field' => 'password', 'label' => 'password', 'rules' => 'trim|required|xss_clean|md5'), 
			array('field' => 'email', 'label' => 'email', 'rules' => 'trim|required|xss_clean')
		);
		$this->form_validation->set_error_delimiters('', '')->set_rules($validation_config);
		if ($this->form_validation->run() === FALSE) {
			$this->core_controller->fail_response(2, validation_errors());
		}
	
	
		// Login
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
		
		// Return JSON

		$this->core_controller->add_return_data('session_token', $new_session_token['session_token']);
		$this->core_controller->add_return_data('expire_time', $new_session_token['expire_time']);

		$this->core_controller->successfully_processed();
	}

	/**
	*  DESC: Logout
	*  
	*/
	public function logout_get()
	{
		// expire current passenger session token
		/*$this->load->model('session_model');
		$this->load->model('user_model');
		$current_user = $this->core_controller->get_current_user();


		$this->session_model->expire_session($current_user[$this->user_model->KEY_did], $this->user_type);

		$this->core_controller->successfully_processed();*/

		//$this->session_model->expire_session($current_user[$this->user_model->KEY_user_id], $this->user_type);
		//$this->core_controller->add_return_data('user_id', $current_user[$this->user_model->KEY_user_id]);*/ 
		//$this->core_controller->successfully_processed();

		
	}
	
	/**
	*  DESC: FB lognlin
	* 
	*/
	public function fblogin_post()
	{
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
	                $this->user_model->KEY_user_name => $fb_user['first_name'].' '.$fb_user['last_name'],
	                $this->user_model->KEY_status => '1' ,
	                $this->user_model->KEY_email => $fb_user['email'],
       			 );
       			 $user_id = $this->user_model->add_user($data);

		         if ($user_id < 0) {
		                $this->core_controller->fail_response(5);
		         }	
		
			    $new_session_token = $this->get_valid_session_token_for_user($user_id);
			    $user_data = $this->user_model->get_user_by_id($user_id);		
	        }

	        //update fb access token for application use
	        $this->user_model->update_fb_access_token($user_data[$this->user_model->KEY_user_id],$accessToken_fb);

	        foreach ($this->hide_user_data($user_data) as $key => $value) {
				$this->core_controller->add_return_data($key, $value);
			}

	        $new_session_token = $this->get_valid_session_token_for_user($user_data[$this->user_model->KEY_user_id]);
	        $this->core_controller->add_return_data('session_token', $new_session_token['session_token'])
							->add_return_data('expire_time', $new_session_token['expire_time']);
            $this->core_controller->successfully_processed();
            
        }
		
		
	}

	/**
	*  DESC: get profile information for a specific user
	*  INPUT: user_id
	*/
	public function get_profile(){
		// Validation
		/*$this->load->library('form_validation');
		$validation_config = array(
			array('field' => 'user_id', 'label' => 'user_id', 'rules' => 'trim|required|xss_clean')
		);
		$this->form_validation->set_error_delimiters('', '')->set_rules($validation_config);
		if ($this->form_validation->run() === FALSE) {
			$this->core_controller->fail_response(2, validation_errors());
		}
		
		// Retrive user information
		$this->load->model('user_model');
		$user_data = $this->user_model->get_user_by_id($this->input->post('user_id'));
		if (count($user_data) == 0) {
			// user of the corresponding user id does not exist
			$this->core_controller->fail_response(3);
		}
		
		// Return JSON
		foreach ($this->hide_user_data($user_data) as $key => $value){
			$this->core_controller->add_return_data($key, $value);
		}
		$this->core_controller->successfully_processed();*/
	}
	
	/**
	*  DESC: edit profile for a specific user
	*  
	*/
	public function edit_profile(){/*
		// Validation (TODO)
		$this->load->library('form_validation');
		$validation_config = array(
			array('field' => 'user_id', 'label' => 'user_id', 'rules' => 'trim|required|xss_clean')
		);
		if ($this->form_validation->run() === FALSE) {
			$this->core_controller->fail_response(2, validation_errors());
		}
		
		// Edit user profile (TODO)
		
		
		// Return JSON
		$this->core_controller->successfully_processed();*/
	}
	
	/****************
	 * helper function 
	 *****************/
	 
	private function get_valid_session_token_for_user($id) {
		$this->load->model('session_model');
		$result = $this->session_model->session_token_based_on_id($id, $this->user_type);
        if (!is_null($result) && is_array($result) && count($result) > 0) {
            // has session token, check
       		if (!$result['expired']) {
		    	return $this->session_model->get_session_by_id($id, $this->user_type);
		    }
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