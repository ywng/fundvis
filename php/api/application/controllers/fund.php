<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/**
 * Fund
 */

require_once (APPPATH. 'libraries/REST_Controller.php');

class Fund extends REST_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->library('CORE_Controller');
		$this->load->helper(array('form', 'url'));
		$this->core_controller->set_response_helper($this);
	
	}

	var $user_type = '';

	public function index()
	{
		
	}

	public getAllFunds_get(){
		$this->load->model('fund_model'); 
		$funds = $this->fund_model->getAllFunds();
		$this->core_controller->add_return_data('funds', $funds); 
		$this->core_controller->successfully_processed();

	}
	
	

	

}

/* End of file fund.php */
/* Location: ./application/controllers/fund.php */