<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/**
 * Notifications Mgt
 * Notifications are triggered by alerts in the Alert table
 * or by the transactions' target / stop loss price
 */

require_once (APPPATH. 'libraries/REST_Controller.php');
require_once (APPPATH. 'libraries/send.message.php'); 
require_once (APPPATH. 'libraries/Facebook.php'); //sdk provided by facebook, you need one for tokens and

class Notification extends REST_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->library('CORE_Controller');
		$this->core_controller->set_response_helper($this);

		$this->load->model('notification_model'); 
		$this->load->model('alert_model'); 
		$this->load->model('user_model');

		$CI = & get_instance();
		$CI->config->load("facebook",TRUE);
		$config = $CI->config->item('facebook');
		$this->load->library('Facebook', $config);
	
	}

	var $user_type = 'user';

	public function alert_check_get()
	{

		$this->load->model('stock_model'); 

		$alerts=$this->alert_model->get_all_active_alerts_all_users();
		if($alerts!=null){

			foreach($alerts as $alert){
				/** check expired
				 *  if expired, skip the loop and set the alert to disable too
				 *  if no expiration date set, assume never expire
				 */
				$db_alert_expire_date=$alert[$this->alert_model->KEY_valid_till];
				if($db_alert_expire_date!=null){
					$expire_date = new DateTime($alert[$this->alert_model->KEY_valid_till]." 23:59:59");
					$now_date = new DateTime("now");
					//dump($now_date > $expire_date);
					if($now_date > $expire_date){
						$data = array(
					  	  $this->alert_model->KEY_enable=>"0"
						);
						$this->alert_model->update_alert($data,$alert[$this->alert_model->KEY_alert_id]);
						continue;
					}
				}

				$notify_type=$alert[$this->alert_model->KEY_type];

				$stock_code=$alert[$this->alert_model->KEY_stock_id];
				$uid=$alert[$this->alert_model->KEY_user_id];
			
				$stock_code_curr_price=$this->stock_model->get_curr_stock_price_by_id($stock_code)["price"];
				$stock=$this->stock_model->get_stock_by_id($stock_code);
				$stock_previous_close=$stock[$this->stock_model->KEY_previous_close];
				if($stock_previous_close==null || $stock_code_curr_price==null ){
					continue;//meaningless to have alert if stock dont's have previous close / current price, mean the stock is suspended or ...not in our db
				}else{
					$daily_percent_chg=($stock_code_curr_price-$stock_previous_close)/($stock_previous_close*1.0)*100;
				}
				
				//notify rules according to types
			
				$msg="Stock Code: ".$stock_code."\r\n";
				$msg=$msg."Stock Name: ".$stock[$this->stock_model->KEY_name]."\r\n";
				$msg=$msg."Current Price: ".$stock_code_curr_price."\r\n\r\n";

				$title="[".$stock_code."] Alert: ".$this->alert_model->get_notification_type_by_id($notify_type);

				$specified_price=$alert[$this->alert_model->KEY_specified_price];
				$daily_percent=$alert[$this->alert_model->KEY_daily_percent];
				$renotify_val=$alert[$this->alert_model->KEY_renotify_diff_percent];
				
				if($notify_type==3){//Greater Than Specified Price
					if($stock_code_curr_price>=$specified_price){
						$msg=$msg."The current price is equal to or greater than specified price (".$specified_price.")";
						$this->check_need_notify($uid,$stock_code,$notify_type,$stock_code_curr_price,$renotify_val,$msg,$title);

					}

				}else if ($notify_type==4){
					if($stock_code_curr_price<=$specified_price){
						$msg=$msg."The current price is equal to or lower than specified price (".$specified_price.")";
						$this->check_need_notify($uid,$stock_code,$notify_type,$stock_code_curr_price,$renotify_val,$msg,$title);
					}

				}else if ($notify_type==5){
					if($stock_previous_close!=0 && $daily_percent_chg>0 && $daily_percent_chg>=$daily_percent){
					
						$msg=$msg."The daily percentage increase (+".$daily_percent_chg."%) is equal to or greater than specified percentage (+".$daily_percent."%)";
						$this->check_need_notify($uid,$stock_code,$notify_type,$daily_percent,$renotify_val,$msg,$title);
					}

				}else if ($notify_type==6){
					if($stock_previous_close!=0 && $daily_percent_chg<0 && ($daily_percent_chg*-1)>=$daily_percent){
					
						$msg=$msg."The daily percentage decrease (".$daily_percent_chg."%) is equal to or greater than specified percentage (-".$daily_percent."%)";
						$this->check_need_notify($uid,$stock_code,$notify_type,$daily_percent,$renotify_val,$msg,$title);
					}
				}




			}


		}

		$this->core_controller->successfully_processed();

	}
	
	public function routine_trans_target_stop_loss_check_get()
	{
		$this->load->model('transaction_model'); 
		$this->load->model('stock_model'); 
		$trans_array=$this->transaction_model->get_all_trans_record_need_notify();

		foreach($trans_array as $trans){
			$trans_type=$trans[$this->transaction_model->KEY_type];
			if($trans_type=="Sell")
				continue; //sell trans no need check

			$stock_code=$trans[$this->transaction_model->KEY_stock_id];
			$target_price=$trans[$this->transaction_model->KEY_target_price];
			$stop_loss_price=$trans[$this->transaction_model->KEY_stop_loss_price];

			$uid=$trans[$this->transaction_model->KEY_user_id];

			$stock_code_curr_price=$this->stock_model->get_curr_stock_price_by_id($stock_code)["price"];
			$stock=$this->stock_model->get_stock_by_id($stock_code);


			//notification rules

			$notify_type=-1;
			$notification_id=-1;
			$title="";
			$msg="Stock Code: ".$stock_code."\r\n";
			$msg=$msg."Stock Name: ".$stock[$this->stock_model->KEY_name]."\r\n";
			$msg=$msg."Current Price: ".$stock_code_curr_price."\r\n\r\n";
			$renotify_val;

			if($target_price!=0 && $stock_code_curr_price>=$target_price){

				// rule 1: greater than target price
				// & 
				// not notify before || the curr price diff from the price of last notification by 1%
				$notify_type=1;//type is 1 too
				$msg=$msg."The current price is equal to or greater than target price (".$target_price.")";
				$title="[".$stock_code."] Target Price Notification ";
			
				$renotify_val=$trans[$this->transaction_model->KEY_target_price_renotify_percent];

			}else if($stop_loss_price!=0 && $stock_code_curr_price<=$stop_loss_price){
			
				// rule 2: smaller than stop loss price
				// & 
				// not notify before || the curr price diff from the price of last notification by 1%
				$notify_type=2;
				$msg=$msg."The current price is equal to or lower than stop loss price (".$stop_loss_price.")";
				$title="[".$stock_code."] Stop Loss Notification ";
				$renotify_val=$trans[$this->transaction_model->KEY_stop_loss_price_renotify_percent];
			}

			if($notify_type!=-1){//will notify
				if($renotify_val==0)// ==0 means that the renotify val is not set, no need renotify after reaching the target price or lower than stop loss price
					$renotify_val=100; //set to a large val, so it wont happen, almost unreachable value 100%, if it really happens, worth to renotify ar haha
				$this->check_need_notify($uid,$stock_code,$notify_type,$stock_code_curr_price,$renotify_val,$msg,$title);

			}//end do notify

		}//end for each

		$this->core_controller->successfully_processed();

	}

	//===========================private functions=========
	private function check_need_notify($uid,$stock_code,$notify_type,$val_at_notify,$renotify_val,$msg,$title)
	{
		$last_notification=$this->notification_model->get_notification($uid,$stock_code,$notify_type);

		if(!$last_notification){
			//no last notification, so do notify
			$notification_data = array(
               $this->notification_model->KEY_stock_id => $stock_code,
               $this->notification_model->KEY_user_id => $uid,
               $this->notification_model->KEY_type => $notify_type,
               $this->notification_model->KEY_val_at_notify => $val_at_notify,
               $this->notification_model->KEY_msg => $msg,
		    );

			$notification_id=$this->notification_model->add_record($notification_data);
			$this->notify($uid,$title,$msg,$notification_id);

		}else{
			$val_at_last_notify=$last_notification[$this->notification_model->KEY_val_at_notify];
			
			if($notify_type==5 || $notify_type==6){//daily percentage change
				$percent_diff=abs($val_at_last_notify-$val_at_notify);

			}else{//price related
				$percent_diff=abs(($val_at_last_notify-$val_at_notify)/($val_at_notify*1.0)*100);

			}

			if($percent_diff>$renotify_val){
				//the curr price diff from the price of last notification by specified %
				$notification_id=$last_notification[$this->notification_model->KEY_notification_id];
				$notification_data = array(
					$this->notification_model->KEY_val_at_notify => $val_at_notify,
				);
				$this->notification_model->update_record($notification_id,$notification_data);
				$this->notify($uid,$title,$msg,$notification_id);

			}


		}

	}

	private function notify($uid,$title,$msg,$notification_id)
	{

		$this->notify_email($uid,$title,$msg,$notification_id);
		$this->notify_fb($uid,$title,$msg,$notification_id);

	}

	private function notify_fb($uid,$title,$msg,$notification_id)
	{
		/* connect fb for sending message */
		$user=$this->user_model->get_user_by_id($uid);
		$fb_access_token=$user[$this->user_model->KEY_fb_access_token];
		
		if($fb_access_token==null)
		  return;

		$this->facebook->setAccessToken($fb_access_token);
        $userfbId = $this->facebook->getUser();
        var_dump($userfbId);
        // If user is not yet authenticated, the id will be zero
        if($userfbId == 0){
            // invalid access token, return with error
             $data['url'] = $this->facebook->getLoginUrl(array('scope'=>'public_profile,email,read_mailbox,xmpp_login'));
             $this->core_controller->add_return_data('login_url', $data['url']); 
             //$this->core_controller->fail_response(5);
        } else {

        	$messageobj=new SendMessage($this->facebook);

			$receiverId=$userfbId; // this may either be username or userID, this class takes care of both the //cases
			$body=$msg;
			if($messageobj->sendMessage($body,$receiverId))
			{
				echo 'message sent';
			}else
			{
				echo 'some error occured';
			}

        }
	}
		
	private function notify_email($uid,$title,$msg,$notification_id)
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

		$user=$this->user_model->get_user_by_id($uid);

		$this->load->library('email', $config);
		$this->email->set_newline("\r\n");
		$this->email->from('WealthVis@gmail.com', 'StockVis');
		$this->email->to($user[$this->user_model->KEY_email]); 
		$this->email->subject($title.' (Notification ID:'.$notification_id.")");
		$this->email->message($msg);	

		$this->email->send();

	
	}
	

}

/* End of file notification.php */
/* Location: ./application/controllers/notification.php */