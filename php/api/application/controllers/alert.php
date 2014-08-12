<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/**
 * Alets Mgt (Notification triggers)
 * It's user set-up triggers
 */

require_once (APPPATH. 'libraries/REST_Controller.php');

class Alert extends REST_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->library('CORE_Controller');
		$this->core_controller->set_response_helper($this);

		$this->load->model('alert_model'); 
	
	}

	var $user_type = 'user';

	public function addAlerts_post(){
		
		$user=$this->core_controller->get_current_user();
		/*$stock_id=$this->input->post('stock_id');
		$price=$this->input->post('price');
		$quantity=$this->input->post('quantity');
		$datetime=$this->input->post('datetime');
		$target_price=$this->input->post('target_price');
		$stop_loss_price=$this->input->post('stop_loss_price');
		$target_price_renotify_percent=$this->input->post('target_price_renotify_percent');
		$stop_loss_price_renotify_percent=$this->input->post('stop_loss_price_renotify_percent');
		$rationale=$this->input->post('rationale');
		$review=$this->input->post('review');
		$type=$this->input->post('type');
		$trans_fee=$this->input->post('trans_fee');*/

		$uid=$user[$this->user_model->KEY_user_id];

		/*$data = array(
		    $this->transaction_model->KEY_user_id =>$uid,
		    $this->transaction_model->KEY_stock_id => $stock_id ,
		    $this->transaction_model->KEY_price => $price,
		    $this->transaction_model->KEY_quantity => $quantity,
		    $this->transaction_model->KEY_datetime => $datetime ,
		    $this->transaction_model->KEY_target_price=> $target_price,
		    $this->transaction_model->KEY_stop_loss_price => $stop_loss_price,
		    $this->transaction_model->KEY_target_price_renotify_percent=> $target_price_renotify_percent,
		    $this->transaction_model->KEY_stop_loss_price_renotify_percent => $stop_loss_price_renotify_percent,
		    $this->transaction_model->KEY_rationale => $rationale ,
		    $this->transaction_model->KEY_review => $review,
		    $this->transaction_model->KEY_type => $type,
		    $this->transaction_model->KEY_trans_fee => $trans_fee ,
		  
		);
		//if it is a sell trans, need special handle
		//what if it is buy, what if it is sell hold!!

		$trans_id = $this->transaction_model->add_record($data);
	
		if($trans_id<0){
			$this->core_controller->fail_response(200);
		}*/
		
		//$this->core_controller->add_return_data('transaction_id', $trans_id); 
		$this->core_controller->successfully_processed();


	}

	public function getAlerts_get(){
		$user=$this->core_controller->get_current_user();
		$uid=$user[$this->user_model->KEY_user_id];

		$alerts_given_uid=$this->alert_model->get_all_alerts_by_uid($uid);

		$this->core_controller->add_return_data('alerts', $alerts_given_uid); 
		$this->core_controller->add_return_data('uid', $uid); 
		$this->core_controller->successfully_processed();

	}

	
	

}

/* End of file alert.php */
/* Location: ./application/controllers/alert.php */