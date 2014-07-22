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

    var $KEY_need_notify= 'need_notify';
   
    var $Table_name_trans = 'Transaction';

    function __construct() {
        parent::__construct();
    }
    
    
    public function add_record($data){
        $this->db->insert($this->Table_name_trans, $data);  
        if ($this->db->affected_rows() > 0) {
            return $this->db->insert_id();
        } else {
            return -1;
        }
    }

    public function get_all_trans_record(){
        return $this->db->get($this->Table_name_trans)->result_array();  
        
    }

    public function get_all_trans_record_need_notify(){
        $this->db->where($this->KEY_need_notify,1);
        return $this->db->get($this->Table_name_trans)->result_array();  
        
    }



}

/* end of file transaction_model.php */