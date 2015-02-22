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

	public function addAlert_post(){
		
		$user=$this->core_controller->get_current_user();
		$stock_id=$this->input->post('stock_id');
		$specified_price=$this->input->post('specified_price');
		$alert_type=$this->input->post('alert_type');
		$daily_percent=$this->input->post('daily_percent');
		$renotify_percent=$this->input->post('renotify_percent');
		$valid_till=$this->input->post('valid_till');

		$uid=$user[$this->user_model->KEY_user_id];

		$data = array(
		    $this->alert_model->KEY_user_id =>$uid,
		    $this->alert_model->KEY_stock_id => $stock_id ,
		    $this->alert_model->KEY_type => $alert_type,
		    $this->alert_model->KEY_enable => "1",
		  
		);
		if($renotify_percent!="null"){
			$data[$this->alert_model->KEY_renotify_diff_percent]=$renotify_percent;
		}
		if($daily_percent!="null"){
			$data[$this->alert_model->KEY_daily_percent]=$daily_percent;
		}
		if($specified_price!="null"){
			$data[$this->alert_model->KEY_specified_price]=$specified_price;
		}
		if($valid_till!="null"){
			$data[$this->alert_model->KEY_valid_till]=$valid_till;
		}

		$alert_id = $this->alert_model->add_alert($data);
	
		if($alert_id<0){
			$this->core_controller->fail_response(200);
		}
		
		$this->core_controller->add_return_data('alert_id', $alert_id); 
		$this->core_controller->successfully_processed();


	}

	public function deleteAlert_post(){
		
		$user=$this->core_controller->get_current_user();
		$alert_id_array=explode(",",$this->input->post('alert_id_array'));

		$uid=$user[$this->user_model->KEY_user_id];

		$this->core_controller->add_return_data('delete_success',$this->alert_model->delete_alert($alert_id_array,$uid)); 
		$this->core_controller->successfully_processed();

	}

	public function getAlerts_get(){
		$user=$this->core_controller->get_current_user();
		$uid=$user[$this->user_model->KEY_user_id];

		$alerts_given_uid=$this->alert_model->get_all_alerts_by_uid($uid);

		$alert_type=$this->alert_model->get_all_notification_type();

		$this->core_controller->add_return_data('alerts', $alerts_given_uid);
		$this->core_controller->add_return_data('alert_type', $alert_type);  
		$this->core_controller->add_return_data('uid', $uid); 
		$this->core_controller->successfully_processed();

	}

	public function updateAlert_post(){
		$alert_id=$this->input->post('alert_id');
		$field=$this->input->post('field');
		$value=$this->input->post('value');

		if($field=="enable"){
			$data = array(
		  	  $this->alert_model->KEY_enable=>$value,
			);
		}else if ($field=="specified_price"){
			if($value==""||$value==" "){
				$specified_price=null;
			}else{
				$specified_price=$value;
			}
			$data = array(
		  	  $this->alert_model->KEY_specified_price=>$specified_price,
			);
		}else if ($field=="daily_percent"){
			if($value==""||$value==" "){
				$daily_percent=null;
			}else{
				$daily_percent=$value;
			}
			$data = array(
		  	  $this->alert_model->KEY_daily_percent=>$daily_percent,
			);
		}else if ($field=="renotify_diff_percent"){
			$data = array(
		  	  $this->alert_model->KEY_renotify_diff_percent=>$value,
			);
		}else if ($field=="valid_till"){
			if($value==""||$value==" "){
				$valid_till_date=null;
			}else{
				$valid_till_date=$value;
			}
			$data = array(
		  	  $this->alert_model->KEY_valid_till=>$valid_till_date,
			);
		}else if ($field=="type"){
			$data = array(
		  	  $this->alert_model->KEY_type=>intval($value),
			);
		}else{
			
		}

		if($this->alert_model->update_alert($data,$alert_id)==-1){
			$this->core_controller->fail_response(300);
		}else{
			$this->core_controller->add_return_data('updated_alert',$this->alert_model->get_alert_by_alert_id($alert_id)); 
			$this->core_controller->successfully_processed();
		}

		


	}

	
	

}

/* End of file alert.php */
/* Location: ./application/controllers/alert.php */