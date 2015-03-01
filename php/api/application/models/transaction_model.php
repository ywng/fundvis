<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Transactions
*
 * @author ngyikwai
 */
class Transaction_model extends CI_Model{

    var $KEY_stock_id = 'sid';
    var $KEY_user_id = 'uid';
    var $KEY_trans_id = 'id';
    var $KEY_type = 'type';
    var $KEY_price = 'price';
    var $KEY_quantity = 'quantity';
    var $KEY_datetime = 'datetime';
    var $KEY_trans_fee = 'trans_fee';
    var $KEY_rationale = 'rationale';
    var $KEY_review = 'review';
    var $KEY_target_price = 'target_price';
    var $KEY_stop_loss_price= 'stop_loss_price';
    var $KEY_target_price_renotify_percent = 'target_price_renotify_percent';
    var $KEY_stop_loss_price_renotify_percent= 'stop_loss_price_renotify_percent';

    var $KEY_is_closed= 'is_closed';
   
    var $Table_name_trans = 'Transaction';

    function __construct() {
        parent::__construct();
        $this->load->model('stock_model');
    }
    
    
    public function add_record($data){
        $this->db->insert($this->Table_name_trans, $data);  
        if ($this->db->affected_rows() > 0) {
            return $this->db->insert_id();
        } else {
            return -1;
        }
    }

    public function delete_trans_record($trans_id_array,$uid){
        $this->db->or_where_in($this->KEY_trans_id, $trans_id_array);
        $this->db->where($this->KEY_user_id, $uid);
        $this->db->delete($this->Table_name_trans); 

        if($this->db->affected_rows() > 0)
          return 1 ;
        else
          return -1;
      
    }

    public function update_trans($data,$id){
      $this->db->where($this->KEY_trans_id, $id);
      $this->db->update($this->Table_name_trans, $data); 
    
      if($this->db->affected_rows() > 0)
        return 1 ;
      else
        return -1;
    }

    public function get_all_trans_record(){
        return $this->db->get($this->Table_name_trans)->result_array();  
        
    }

    public function get_trans_record_by_trans_id($id){
        $this->db->where($this->KEY_trans_id,$id);
        $this->db->join($this->stock_model->Table_name_stock, $this->stock_model->Table_name_stock.'.'.$this->stock_model->KEY_stock_id .'='. $this->Table_name_trans.'.'.$this->KEY_stock_id);
        return $this->db->get($this->Table_name_trans)->result_array();  
        
    }

    public function get_all_trans_record_by_uid($uid){
        $this->db->where($this->KEY_user_id,$uid);
        $this->db->join($this->stock_model->Table_name_stock, $this->stock_model->Table_name_stock.'.'.$this->stock_model->KEY_stock_id .'='. $this->Table_name_trans.'.'.$this->KEY_stock_id);
        return $this->db->get($this->Table_name_trans)->result_array();  
        
    }

    public function get_all_open_buy_record_with_uid_sid($stock_id,$uid){
        $this->db->where($this->KEY_stock_id,$stock_id);
        $this->db->where($this->KEY_user_id,$uid);
        $this->db->where($this->KEY_is_closed,0);
        return $this->db->get($this->Table_name_trans)->result_array();  
        
    }

    public function get_all_trans_record_need_notify(){
        $this->db->where($this->KEY_is_closed,0);
        return $this->db->get($this->Table_name_trans)->result_array();  
        
    }



}

/* end of file transaction_model.php */