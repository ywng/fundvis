<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/**
 * Notifications Mgt
 */

require_once (APPPATH. 'libraries/REST_Controller.php');

class Stock extends REST_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->library('CORE_Controller');
		$this->core_controller->set_response_helper($this);

		$this->load->model('notification_model'); 
	
	}

	var $user_type = 'user';

	
	public function routine_trans_target_stop_loss_check_get()
	{
		$this->load->model('transaction_model'); 
		$this->load->model('stock_model'); 
		$trans_array=$this->transaction_model->get_all_trans_record();

		foreach($trans_array as $trans){
			$stock_code=$trans[$this->transaction_model->KEY_stock_id];
			$target_price=$trans[$this->transaction_model->KEY_target_price];
			$stop_loss_price=$trans[$this->transaction_model->KEY_stop_loss_price];

			$uid=$trans[$this->transaction_model->KEY_user_id];

			$stock_code_curr_price=$this->stock_model->get_curr_stock_price_by_id($stock_code)["price"];


			//notification rules

			$notify_type=-1;
			$msg="";
			if($stock_code_curr_price>=$target_price){

				// rule 1: greater than target price
				// & 
				// not notify before || the curr price diff from the price of last notification by 1%
				$notify_type=1;//type is 1 too
				$msg=" is equal to or greater than target price ("+$target_price+")";


			}else if($stock_code_curr_price<=$stop_loss_price){
			
				// rule 2: smaller than stop loss price
				// & 
				// not notify before || the curr price diff from the price of last notification by 1%
				$notify_type=2;
				$msg=" is equal to or lower than stop loss price ("+$stop_loss_price+")";
				
			}

			if($notify_type!=-1){//will notify
				$last_notification=$this->notification_model->get_notification($uid,$stock_code,$notify_type);
				if(!$last_notification){
					//no last notification, so do notify

					$this->notify($uid,$msg);

				}else{
					$val_at_last_notify=$last_notification[$this->notification_model->KEY_val_at_notify];
					$percent_diff=abs(($val_at_last_notify-$stock_code_curr_price)/($stock_code_curr_price*1.0)*100);

					if($percent_diff>1){
						//the curr price diff from the price of last notification by 1%
					
						$this->notify($uid,$msg);

					}

				}


			}//end do notify
			
				


		}//end for each

		$this->core_controller->successfully_processed();

	}

	//===========================private functions=========

	private function notify($uid,$msg)
	{

		$this->notify_email($uid,$msg);

	}

	private function notify_email($uid,$msg)
	{
		var_dump($msg);
	}
	

}

/* End of file notification.php */
/* Location: ./application/controllers/notification.php */