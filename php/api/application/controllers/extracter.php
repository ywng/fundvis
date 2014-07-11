<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/**
 * Extract -- Web Scrapping
 */

require_once (APPPATH. 'libraries/REST_Controller.php');

class Extracter extends REST_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->library('CORE_Controller');
		$this->load->library('simple_html_dom');
		$this->load->helper(array('form', 'url'));
		$this->core_controller->set_response_helper($this);
	
	}

	var $user_type = '';


	public function extract_get(){

		$this->load->model('fund_model'); 
		$funds = $this->fund_model->get_all_funds();

		foreach ($funds as $fund){

			// Create DOM from URL or file
			$html = file_get_html($fund[$this->fund_model->KEY_link]);
			if (strlen(strstr($fund[$this->fund_model->KEY_link],"http://www.jpmorganam.com.hk/jpm/am/"))>0) {
				// JPM webpages
				$this->JPM_extract($html,$fund);

			}else if (strlen(strstr($fund[$this->fund_model->KEY_link],"http://www.bloomberg.com/quote/"))>0){
				// Bloomberg webpages
				$this->bloomberg_extract($html,$fund);
			}

		}

		$this->core_controller->successfully_processed();

	}

	public function stock_extract_get(){

		$this->load->model('stock_model'); 
		$stocks = $this->stock_model->get_all_stocks();

		foreach ($stocks as $stock){

			// Create DOM from URL or file
			$html = file_get_html($stock[$this->stock_model->KEY_link]);
			if (strlen(strstr($stock[$this->stock_model->KEY_link],"http://www.aastocks.com/"))>0) {
				// AASTOCK webpages
				$this->AASTOCK_stock_extract($html,$stock);

			}

		}

		$this->core_controller->successfully_processed();

	}

	private function AASTOCK_stock_extract($html,$stock){
		// price span
		/*$price_e=$html->find('span[class=price]')[0];
		
		$price=preg_replace("/[^0-9.]/", '',$price_e->plaintext);

		//date span
		$date_e=$html->find('p[class=fine_print]')[0];
		//var_dump($date_e);
		if (preg_match('/(0[1-9]|1[012])[\/](0[1-9]|[12][0-9]|3[01])[\/](19|20)[0-9]{2}/',$date_e->plaintext, $regs)) {
			$date_str = $regs[0];
	    } 

	    $fund_daily_price = array(
               $this->fund_model->KEY_name => $fund[$this->fund_model->KEY_name],
               $this->fund_model->KEY_price => $price ,
               $this->fund_model->KEY_datetime => $date_str
        );
        $this->core_controller->add_return_data($fund[$this->fund_model->KEY_fund_id],$fund_daily_price); 

	    $this->fund_model->insert_fund_daily_price($fund[$this->fund_model->KEY_fund_id],$price,$date_str);*/
	}
	

	private function bloomberg_extract($html,$fund){
		// price span
		$price_e=$html->find('span[class=price]')[0];
		
		$price=preg_replace("/[^0-9.]/", '',$price_e->plaintext);

		//date span
		$date_e=$html->find('p[class=fine_print]')[0];
		//var_dump($date_e);
		if (preg_match('/(0[1-9]|1[012])[\/](0[1-9]|[12][0-9]|3[01])[\/](19|20)[0-9]{2}/',$date_e->plaintext, $regs)) {
			$date_str = $regs[0];
	    } 

	    $fund_daily_price = array(
               $this->fund_model->KEY_name => $fund[$this->fund_model->KEY_name],
               $this->fund_model->KEY_price => $price ,
               $this->fund_model->KEY_datetime => $date_str
        );
        $this->core_controller->add_return_data($fund[$this->fund_model->KEY_fund_id],$fund_daily_price); 

	    $this->fund_model->insert_fund_daily_price($fund[$this->fund_model->KEY_fund_id],$price,$date_str);
	}

	private function JPM_extract($html,$fund){

		$element_div=$html->find('div[class=daily_price_box]')[0];
		$raw_str=$element_div->children(1)->children(0)->children(0)->plaintext;
		//var_dump($raw_str);
		$data=explode(" ",$raw_str);
		$price=preg_replace("/[\t]/", '',$data[16]);
		$date_str=preg_replace("/[\t]/", '',$data[2]);
		$date_arr=explode(".",$date_str);
		$date_str="20".$date_arr[2]."-".$date_arr[1]."-".$date_arr[0];

 		$fund_daily_price = array(
               $this->fund_model->KEY_name => $fund[$this->fund_model->KEY_name],
               $this->fund_model->KEY_price => $price ,
               $this->fund_model->KEY_datetime => $date_str
        );
        $this->core_controller->add_return_data($fund[$this->fund_model->KEY_fund_id],$fund_daily_price); 


		$this->fund_model->insert_fund_daily_price($fund[$this->fund_model->KEY_fund_id],$price,$date_str);
	}

	

}

/* End of file extracter.php */
/* Location: ./application/controllers/extracter.php */