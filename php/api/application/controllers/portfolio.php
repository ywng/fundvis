<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/**
 * Portfolio
 */

require_once (APPPATH. 'libraries/REST_Controller.php');

class Portfolio extends REST_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->library('CORE_Controller');
		$this->load->helper(array('form', 'url'));
		$this->core_controller->set_response_helper($this);

		$this->load->model('portfolio_model'); 
	
	}

	var $user_type = '';

	public function getAllStocksInAnyPortfolio(){	
		return $stockList=$this->portfolio_model->get_all_stock_code_in_any_portfolio();
	}

	

}

/* End of file stock.php */
/* Location: ./application/controllers/stock.php */