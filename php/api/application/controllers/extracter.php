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
		$funds = $this->fund_model->getAllFunds();

		foreach ($funds as $fund){
			if (strlen(strstr($fund[$this->fund_model->KEY_link],"http://www.jpmorganam.com.hk/jpm/am/"))>0) {
				// JPM webpages
				$this->JPM_extract($fund[$this->fund_model->KEY_link]);

			}else if (strlen(strstr($fund[$this->fund_model->KEY_link],"http://www.bloomberg.com/quote/"))>0){
				// Bloomberg webpages
				$this->bloomberg_extract($fund[$this->fund_model->KEY_link]);
			}

		}


	}
	

	private function bloomberg_extract($url){
		// Create DOM from URL or file
		$html = file_get_html($url);

		// Find all spans
		$price_e=$html->find('span[class=price]')[0];
		$price=$price_e->find('meta[itemprop=price]');
		echo $price->content;
		      
		/*// Find all links 
		foreach($html->find('a') as $element) 
		       echo $element->href . '<br>';*/
		


	}

	private function JPM_extract($url){


		
	}
	

	

}

/* End of file extracter.php */
/* Location: ./application/controllers/extracter.php */