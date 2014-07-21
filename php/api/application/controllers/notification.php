<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/**
 * Notifications Mgt
 */

require_once (APPPATH. 'libraries/REST_Controller.php');

class Stock extends REST_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->library('CORE_Controller');
		$this->core_controller->set_response_helper($this);

		$this->load->model('notification_model'); 
	
	}

	var $user_type = 'user';

	
	

}

/* End of file notification.php */
/* Location: ./application/controllers/notification.php */