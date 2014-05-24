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


	public function getAllFunds_get(){
		$this->load->model('fund_model'); 
		$funds = $this->fund_model->get_all_funds();
		$this->core_controller->add_return_data('funds', $funds); 
		$this->core_controller->successfully_processed();

	}

	public function getAllPrice_get(){
		$this->load->model('fund_model'); 
		$funds = $this->fund_model->get_all_funds();

		$funds_price= array();
		
		foreach($funds as $fund){
			$fund_price_object = array(
               "price_array" => $this->fund_model->get_fund_price_by_id($fund[$this->fund_model->KEY_fund_id]),
               "id" =>$fund[$this->fund_model->KEY_fund_id],
               "name"=> $fund[$this->fund_model->KEY_name]
            );
			
			array_push($funds_price,$fund_price_object);
		}
		$this->core_controller->add_return_data('funds', $funds_price); 

		$max_date=$this->fund_model->get_max_date();
		$this->core_controller->add_return_data('max_date', $max_date[0]["datetime"]); 
		$min_date=$this->fund_model->get_min_date();
		$this->core_controller->add_return_data('min_date', $min_date[0]["datetime"]); 

		$this->core_controller->successfully_processed();

	}

	public function getFundPrice_post(){


	}
	
	

	

}

/* End of file fund.php */
/* Location: ./application/controllers/fund.php */