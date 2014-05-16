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
				// Create DOM from URL or file
		$html = file_get_html('http://www.google.com/');

		// Find all images 
		foreach($html->find('img') as $element) 
		       echo $element->src . '<br>';

		// Find all links 
		foreach($html->find('a') as $element) 
		       echo $element->href . '<br>';
		

	}
	
	

	

}

/* End of file extracter.php */
/* Location: ./application/controllers/extracter.php */