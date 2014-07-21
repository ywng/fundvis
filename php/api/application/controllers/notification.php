<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/**
 * Notifications Mgt
 */

require_once (APPPATH. 'libraries/REST_Controller.php');

class Notification extends REST_Controller {

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
			$stock=$this->stock_model->get_stock_by_id($stock_code);


			//notification rules

			$notify_type=-1;
			$msg="Stock Code: ".$stock_code.'\n';
			$msg=$msg."Stock Name: ".$stock[$this->stock_model->KEY_name].'\n';
			$msg=$msg."Current Price: ".$stock_code_curr_price.'\n\n';

			if($stock_code_curr_price>=$target_price){

				// rule 1: greater than target price
				// & 
				// not notify before || the curr price diff from the price of last notification by 1%
				$notify_type=1;//type is 1 too
				$msg="The current price is equal to or greater than target price (".$target_price.")";


			}else if($stock_code_curr_price<=$stop_loss_price){
			
				// rule 2: smaller than stop loss price
				// & 
				// not notify before || the curr price diff from the price of last notification by 1%
				$notify_type=2;
				$msg="The current price is equal to or lower than stop loss price (".$stop_loss_price.")";
				
			}

			if($notify_type!=-1){//will notify
				$last_notification=$this->notification_model->get_notification($uid,$stock_code,$notify_type);

				if(!$last_notification){
					//no last notification, so do notify
					$notification_data = array(
		               $this->notification_model->KEY_stock_id => $stock_code,
		               $this->notification_model->KEY_user_id => $uid,
		               $this->notification_model->KEY_type => $notify_type,
		               $this->notification_model->KEY_val_at_notify => $stock_code_curr_price,
		               $this->notification_model->KEY_msg => $msg,
      		    	);

					$notification_id=$this->notification_model->add_record($notification_data);
					$this->notify($uid,$msg,$notification_id);

				}else{
					$val_at_last_notify=$last_notification[$this->notification_model->KEY_val_at_notify];
					$percent_diff=abs(($val_at_last_notify-$stock_code_curr_price)/($stock_code_curr_price*1.0)*100);

					if($percent_diff>1){
						//the curr price diff from the price of last notification by 1%
						$notification_id=$last_notification[$this->notification_model->KEY_notification_id];
						$notification_data = array(
							$this->notification_model->update_record($notification_data);
							$this->notification_model->KEY_val_at_notify => $stock_code_curr_price,
						);
						$this->notify($uid,$msg,$notification_id);

					}

				}


			}//end do notify
			
				


		}//end for each

		if($notification_id){
			$this->core_controller->add_return_data('notification_id', $notification_id); 
		}

		$this->core_controller->successfully_processed();

	}

	//===========================private functions=========

	private function notify($uid,$msg,$notification_id)
	{

		$this->notify_email($uid,$msg,$notification_id);

	}

	private function notify_email($uid,$msg,$notification_id)
	{
		var_dump($msg);
	}
	

}

/* End of file notification.php */
/* Location: ./application/controllers/notification.php */