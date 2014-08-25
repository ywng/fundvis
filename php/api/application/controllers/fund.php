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

		$this->load->model('fund_model'); 
	
	}

	var $user_type = '';


	public function getAllFunds_get(){
		$this->load->model('fund_model'); 
		$funds = $this->fund_model->get_all_funds();
		$this->core_controller->add_return_data('funds', $funds); 
		$this->core_controller->successfully_processed();

	}

	public function getAllFundsJPMORSO_get(){
		$this->load->model('fund_model'); 
		$funds = $this->fund_model->get_all_funds();
		$this->core_controller->add_return_data('funds', $funds); 
		$this->core_controller->successfully_processed();

	}

	public function getAllPrice_get(){
		 
		$funds = $this->fund_model->get_all_funds();

		$max_date=$this->fund_model->get_max_date();
		$this->core_controller->add_return_data('max_date', $max_date[0]); 
		$min_date=$this->fund_model->get_min_date();
		$this->core_controller->add_return_data('min_date', $min_date[0]); 

		$this->retriveRequiredFundsData($funds);

	}

	public function getAllPriceJPMORSO_get(){
		
		$requiredFundIDArr= array();
		for($i=1;$i<=20;$i++){//JPM ORSO consists of funds with fund id [1,2,3.....20]
			array_push($requiredFundIDArr,$i);
		}

		$funds = $this->fund_model->get_funds_with_id_array($requiredFundIDArr);


		$max_date=$this->fund_model->get_max_date_with_id_array($requiredFundIDArr);
		$this->core_controller->add_return_data('max_date', $max_date[0]); 
		$min_date=$this->fund_model->get_min_date_with_id_array($requiredFundIDArr);
		$this->core_controller->add_return_data('min_date', $min_date[0]); 
		
		$this->retriveRequiredFundsData($funds);
	}


	//=====================private helper functions====================================//
	private function retriveRequiredFundsData($funds){
		$funds_price= array();
		
		foreach($funds as $fund){
			$fund_price_object = array(
               "price_array" => $this->fund_model->get_fund_price_by_id($fund[$this->fund_model->KEY_fund_id]),
               "id" =>$fund[$this->fund_model->KEY_fund_id],
               "name"=> $fund[$this->fund_model->KEY_name],
               "link" => $fund[$this->fund_model->KEY_link]
            );
			
			array_push($funds_price,$fund_price_object);
		}
		$this->core_controller->add_return_data('funds', $funds_price); 

		$this->core_controller->successfully_processed();
	}
	
	

	

}

/* End of file fund.php */
/* Location: ./application/controllers/fund.php */