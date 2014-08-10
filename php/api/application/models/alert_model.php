<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Alerts Triggers
*
 * @author ngyikwai
 */
class Alert_model extends CI_Model{

    //alert table
    var $KEY_alert_id = 'id';
    var $KEY_type = 'type';
    var $KEY_stock_id = 'sid';
    var $KEY_user_id = 'uid';
    var $KEY_specified_price = 'specified_price';
    var $KEY_daily_percent = 'daily_percent';
    var $KEY_renotify_diff_percent = 'renotify_diff_percent';
    var $Table_name_alert = 'Alert';

    //notification type table
    var $KEY_notification_type_id = 'id';
    var $KEY_notification_type_str = 'type';
    var $Table_name_notification_type = 'NotificationType';


    function __construct() {
        parent::__construct();
    }
    
   
    //====================================================//
  
   public function get_all_alerts(){
       $q=$this->db->get($this->Table_name_alert);
        if( $q->num_rows() >0){
            return $q->result_array();
        } else{
            return null;
        }

   }

   public function add_alert($data){
        $this->db->insert($this->Table_name_alert, $data); 
        if ($this->db->affected_rows() > 0) {
            return $this->db->insert_id();
        } else {
            return -1;
        }
   }

   //===========================================================
   //notification type
   public function get_notification_type_by_id($id){
        $this->db->where($this->KEY_notification_type_id,$id);
        $q=$this->db->get($this->Table_name_notification_type);
        if( $q->num_rows() >0){
            $row=$q->result_array()[0];
            return $row[$this->KEY_notification_type_str];
        } else{
            return null;
        }
   }



}

/* end of file alert_model.php */