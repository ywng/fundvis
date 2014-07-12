<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/**
 * Stocks
 */

require_once (APPPATH. 'libraries/REST_Controller.php');

class Stock extends REST_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->library('CORE_Controller');
		$this->load->helper(array('form', 'url'));
		$this->core_controller->set_response_helper($this);

		$this->load->model('stock_model'); 
	
	}

	var $user_type = '';

	public function addStockByCode_post(){
		$this->load->library('../controllers/extracter');
		$codes=explode("::",$this->input->post('code'));

		$added_stocks=array();
		foreach ($codes as $code){
			$stock_info=$this->extracter->AASTOCK_stock_getinfo($code);
			$category=$this->stock_model->getCategoryID($stock_info["category"]);
			
			$stock_data = array(
	               $this->stock_model->KEY_stock_id => $code,
	               $this->stock_model->KEY_name => $stock_info["name"],
	               $this->stock_model->KEY_link => "http://www.aastocks.com/en/ltp/rtquote.aspx?symbol=".$code ,
	               $this->stock_model->KEY_valid => 1,
	               $this->stock_model->KEY_category => $category,
	        );
			$this->stock_model->addStock($stock_data);
			array_push($added_stocks,$stock_data);

		}
		
		$this->core_controller->add_return_data('added_stocks', $added_stocks); 
		$this->core_controller->successfully_processed();

	}

	public function getAllStocks_get(){
		$stocks = $this->stock_model->get_all_stocks();
		$this->core_controller->add_return_data('stocks', $stocks); 
		$this->core_controller->successfully_processed();

	}

	

	public function getAllPrice_get(){
		 
		$stocks = $this->stock_model->get_all_stocks();

		$max_date=$this->stock_model->get_max_date();
		$this->core_controller->add_return_data('max_date', $max_date[0]); 
		$min_date=$this->stock_model->get_min_date();
		$this->core_controller->add_return_data('min_date', $min_date[0]); 

		$this->retriveRequiredStocksData($stocks);

	}

	

	//=====================private helper functions====================================//

	private function retriveRequiredStocksData($stocks){
		$stocks_price= array();
		
		foreach($stocks as $stock){
			$stock_price_object = array(
               "price_array" => $this->stock_model->get_stock_price_by_id($stock[$this->stock_model->KEY_stock_id]),
               "id" =>$stock[$this->stock_model->KEY_stock_id],
               "name"=> $stock[$this->stock_model->KEY_name]
            );
			
			array_push($stocks_price,$stock_price_object);
		}
		$this->core_controller->add_return_data('stocks', $stocks_price); 

		$this->core_controller->successfully_processed();
	}
	

	

}

/* End of file stock.php */
/* Location: ./application/controllers/stock.php */