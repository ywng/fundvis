<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/**
 * Transactions
 */

require_once (APPPATH. 'libraries/REST_Controller.php');

class Transaction extends REST_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->library('CORE_Controller');
		$this->core_controller->set_response_helper($this);
		
		$this->load->model('transaction_model'); 
	}

	var $user_type = '';

	public function getSellableQuantity_post(){
		$this->load->model('user_model'); 
		$user=$this->core_controller->get_current_user();
		$uid=$user[$this->user_model->KEY_user_id];

		$stock_id=$this->input->post('code');

		$sellable_quantity=$this->get_all_open_buys_quantity($stock_id,$uid);
		$this->core_controller->add_return_data('sellable_quantity', $sellable_quantity); 

		$this->core_controller->successfully_processed();
	}

	public function getUserTransRecord_get(){
		$this->load->model('user_model'); 
		$user=$this->core_controller->get_current_user();
		$uid=$user[$this->user_model->KEY_user_id];

		$user_trans_record=$this->transaction_model->get_all_trans_record_by_uid($uid);
		$this->core_controller->add_return_data('user_trans_record', $user_trans_record); 

		$this->core_controller->successfully_processed();
	}
	
	public function addTransactionRecord_post(){
		
		$this->load->model('user_model'); 
		$user=$this->core_controller->get_current_user();
		$stock_id=$this->input->post('stock_id');
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
		$trans_fee=$this->input->post('trans_fee');

		$uid=$user[$this->user_model->KEY_user_id];

		if($type=="Sell"||$type=="Hold"){
			$this->check_hold_sell_conditions($stock_id,$uid,$quantity);

			if($type=="Sell"){
				$this->sell_handling();
			}
		}

		//pass check or it is buy type
		$data = array(
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
		}
		
		$this->core_controller->add_return_data('transaction_id', $trans_id); 
		$this->core_controller->successfully_processed();


	}

	public function deleteTransactionRecord_post(){
		
		$user=$this->core_controller->get_current_user();
		$trans_id_array=explode(",",$this->input->post('trans_id_array'));

		$uid=$user[$this->user_model->KEY_user_id];

		$this->core_controller->add_return_data('delete_success',$this->transaction_model->delete_trans_record($trans_id_array,$uid)); 
		$this->core_controller->successfully_processed();

	}

	public function updateTransactionRecord_post(){
		$trans_id=$this->input->post('trans_id');
		$field=$this->input->post('field');
		$value=$this->input->post('value');

		if($field=="rationale"){
			$data = array(
		  	  $this->transaction_model->KEY_rationale=>$value,
			);
		}else if ($field=="review"){
			$data = array(
		  	  $this->transaction_model->KEY_review=>$value,
			);
		}else if ($field=="target_price"){
			$data = array(
		  	  $this->transaction_model->KEY_target_price=>$value,
			);
		}else if ($field=="target_price_renotify_percent"){
			$data = array(
		  	  $this->transaction_model->KEY_target_price_renotify_percent=>$value,
			);
		}else if ($field=="stop_loss_price"){
			$data = array(
		  	  $this->transaction_model->KEY_stop_loss_price=>$value,
			);
		}else if ($field=="stop_loss_price_renotify_percent"){
			$data = array(
		  	  $this->transaction_model->KEY_stop_loss_price_renotify_percent=>$value,
			);
		}else{
			
		}

		if($this->transaction_model->update_trans($data,$trans_id)==-1){
			$this->core_controller->fail_response(400);
		}else{
			$this->core_controller->add_return_data('updated_trans',$this->transaction_model->get_trans_record_by_trans_id($trans_id)); 
			$this->core_controller->successfully_processed();
		}

		


	}

	//helper functions
	private function check_hold_sell_conditions($stock_id,$uid,$quantity){
		if($this->get_all_open_buys_quantity($stock_id,$uid)==-1){
			$this->core_controller->fail_response(201);
		}else if($this->get_all_open_buys_quantity($stock_id,$uid)<$quantity){
			$this->core_controller->fail_response(202);
		}
	}

	private function sell_handling(){

	}

	private function get_all_open_buys_quantity($stock_id,$uid){
		$open_buys=$this->transaction_model->get_all_open_buy_record_with_uid_sid($stock_id,$uid);
		
		if(count($open_buys)<1)
			return -1;
		$quantity=0;
		foreach($open_buys as $buy){
			$quantity+=$buy[$this->transaction_model->KEY_quantity];
		}

		return $quantity;

	}
	 

	

}

/* End of file transaction.php */
/* Location: ./application/controllers/transaction.php */