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
    var $KEY_enable = 'enable';
    var $KEY_valid_till = 'valid_till';
    var $Table_name_alert = 'Alert';


    //notification type table
    var $KEY_notification_type_id = 'id';
    var $KEY_notification_type_str = 'type';
    var $Table_name_notification_type = 'NotificationType';




    function __construct() {
        parent::__construct();
        $this->load->model('stock_model');
    }
    
   
    //====================================================//
  
   public function get_all_active_alerts_all_users(){
       $this->db->where($this->KEY_enable,"1");
       $q=$this->db->get($this->Table_name_alert);

        if( $q->num_rows() >0){
            return $q->result_array();
        } else{
            return null;
        }

   }

   public function get_all_alerts_by_uid($uid){
       //$this->db->select($this->Table_name_alert.'.*,'.$this->stock_model->Table_name_stock.'.'.$this->stock_model->KEY_name);
       $this->db->join($this->stock_model->Table_name_stock, $this->stock_model->Table_name_stock.'.'.$this->stock_model->KEY_stock_id .'='. $this->Table_name_alert.'.'.$this->alert_model->KEY_stock_id);

       $this->db->where($this->KEY_user_id,$uid);
       $this->db->order_by($this->KEY_stock_id,"asc"); 
       $q=$this->db->get($this->Table_name_alert);
       if( $q->num_rows() >0){
            return $q->result_array();
        } else{
            return null;
        }

   }

   public function get_alert_by_alert_id($id){
       $this->db->where($this->KEY_alert_id,$id);
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

   public function delete_alert($alert_id_array,$uid){
        $this->db->or_where_in($this->KEY_alert_id, $alert_id_array);
        $this->db->where($this->KEY_user_id, $uid);
        $this->db->delete($this->Table_name_alert); 

        if($this->db->affected_rows() > 0)
          return 1 ;
        else
          return -1;
      
   }

   public function update_alert($data,$id){
      $this->db->where($this->KEY_alert_id, $id);
      $this->db->update($this->Table_name_alert, $data); 
    
      if($this->db->affected_rows() > 0)
        return 1 ;
      else
        return -1;
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

   public function get_all_notification_type(){
        $q=$this->db->get($this->Table_name_notification_type);
        if( $q->num_rows() >0){
            return $q->result_array();
        } else{
            return null;
        }
   }


}

/* end of file alert_model.php */