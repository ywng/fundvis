<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Notifications
*
 * @author ngyikwai
 */
class Notification_model extends CI_Model{

    var $KEY_stock_id = 'sid';
    var $KEY_user_id = 'uid';
    var $KEY_notification_id = 'id';
    var $KEY_msg = 'msg';
    var $KEY_type = 'type';
    var $KEY_val_at_notify = 'val_at_notify';
    var $Table_name_notification = 'NotificationHistory';

    //alert table
    var $KEY_alert_id = 'id';
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
   public function get_notification($uid,$sid,$type)
   {
        $this->db->where($this->KEY_stock_id,$sid);
        $this->db->where($this->KEY_user_id,$uid);
        $this->db->where($this->KEY_type,$type);
        
        $q=$this->db->get($this->Table_name_notification);
        if( $q->num_rows() >0){
            return $q->result_array()[0];
        } else{
            return null;
        }


   }

   public function add_record($data)
   {
        $this->db->insert($this->Table_name_notification, $data); 
        if ($this->db->affected_rows() > 0) {
            return $this->db->insert_id();
        } else {
            return -1;
        }

   }

    public function update_record($notification_id,$data)
   {
        $this->db->where($this->KEY_notification_id, $notification_id);
        $this->db->update($this->Table_name_notification, $data); 


   }

   //==========================================================
   //Alert Table 
   public function get_all_alerts(){
       $q=$this->db->get($this->Table_name_alert);
        if( $q->num_rows() >0){
            return $q->result_array();
        } else{
            return null;
        }

   }

   //===========================================================
   //notification type
   public function get_notification_type_by_id($id){
        $this->db->where($this->KEY_notification_type_id,$id);
        $q=$this->db->get($this->Table_name_notification_type);
        if( $q->num_rows() >0){
            return $q->result_array()[0];
        } else{
            return null;
        }
   }



}

/* end of file notification_model.php */