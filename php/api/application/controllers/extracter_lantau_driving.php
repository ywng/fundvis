<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/**
 * Extract -- Web Scrapping
 */

require_once (APPPATH. 'libraries/REST_Controller.php');

class Extracter_lantau_driving extends REST_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->library('CORE_Controller');
		$this->load->library('simple_html_dom');
		$this->load->helper(array('form', 'url'));
		$this->core_controller->set_response_helper($this);
	
	}

	var $user_type = '';


	public function extract_get(){
		$now = new \DateTime('now');
		$month = $now->format('m');
		$year = $now->format('Y');

		$url="https://lantaupermit1.td.gov.hk/lcrp/application/application.do?method=viewLatestQuotasAvailability&month=".$month."&year=".$year;
		
		
		$html = file_get_html("https://lantaupermit1.td.gov.hk/lcrp/application/main.jsp");
		$html = file_get_html($url);
		
		echo $html->plaintext;
		$odd_rows=$html->find('tr[class=RECORD_ODD]');
		
		foreach($odd_rows as $odd_row) {
			echo $odd_row;
		    echo $odd_row->find('td', 2)->plaintext;
		}

	 	$even_rows=$html->find('tr[class=record_even]');

		$this->core_controller->successfully_processed();

	}

	
	//helper functions

	private function notify_email($url)
	{
		//var_dump($msg);//test
	    
	    $config = Array(		
		    'protocol' => 'smtp',
		    'smtp_host' => 'ssl://smtp.googlemail.com',
		    'smtp_port' => 465,
		    'smtp_user' => 'WealthVis@gmail.com',  //use our google ac to send the email
		    'smtp_pass' => 'abcd1919',
		    'smtp_timeout' => '4',
		    'mailtype'  => 'text', 
		    'charset'   => 'iso-8859-1',
		    'wordwrap' => TRUE,
		);

		//$user=$this->user_model->get_user_by_id($uid);

		$this->load->library('email', $config);
		$this->email->set_newline("\r\n");
		$this->email->from('WealthVis@gmail.com', 'ShitGov');
		$this->email->to('victor.not.to.yield@gmail.com'); 
		$this->email->subject("Let's go Lantau Island Driving!");
		$this->email->message(file_get_html($url)->plaintext);	

		$this->email->send();

	
	}

	


}

/* End of file extracter.php */
/* Location: ./application/controllers/extracter.php */