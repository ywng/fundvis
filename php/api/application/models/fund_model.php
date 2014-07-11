<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Funds
*
 * @author ngyikwai
 */
class Fund_model extends CI_Model{

    var $KEY_fund_id = 'id';
    var $KEY_name = 'name';
    var $KEY_link = 'link';
    var $KEY_remark = 'remark';
    var $KEY_valid = 'valid';
    var $Table_name_fund = 'Fund';


    var $Table_name_price = 'FundPrice';
    var $KEY_price_fund_id = 'fid';
    var $KEY_price = 'price';
    var $KEY_datetime = 'datetime';
    

    function __construct() {
        parent::__construct();
    }
    
    
    /*
     * 
     */
    public function get_max_date_with_id_array($requiredFundIDArray){
        $this->db->or_where_in($this->KEY_price_fund_id,$requiredFundIDArray);
        $this->db->select_max($this->KEY_datetime);
        return $this->db->get($this->Table_name_price)->result();
    }

    public function get_min_date_with_id_array($requiredFundIDArray){
        $this->db->or_where_in($this->KEY_price_fund_id,$requiredFundIDArray);
        $this->db->select_min($this->KEY_datetime);
        return $this->db->get($this->Table_name_price)->result();
    }

    public function get_max_date(){
        $this->db->select_max($this->KEY_datetime);
        return $this->db->get($this->Table_name_price)->result();
    }

    public function get_min_date(){
        $this->db->select_min($this->KEY_datetime);
        return $this->db->get($this->Table_name_price)->result();
    }

    public function get_all_funds(){
        $this->db->where($this->KEY_valid,1);
        return $this->db->get($this->Table_name_fund)->result_array();
    }

    public function get_funds_with_id_array($requiredFundIDArray){
        $this->db->or_where_in($this->KEY_fund_id,$requiredFundIDArray);
        $this->db->where($this->KEY_valid,1);
        return $this->db->get($this->Table_name_fund)->result_array();
    }
   

    public function get_all_prices(){
        $this->db->order_by($this->KEY_datetime, "desc"); 
        return $this->db->get($this->Table_name_price)->result_array();
    }

    public function get_fund_price_by_id($id){
        $this->db->order_by($this->KEY_datetime, "desc"); 
        $this->db->select($this->KEY_price.",".$this->KEY_datetime);
        $this->db->where($this->KEY_price_fund_id,$id);
        return $this->db->get($this->Table_name_price)->result_array();
    }

    public function insert_fund_daily_price($id, $price,$date_str){
        $date = new DateTime($date_str);
        $this->db->where($this->KEY_price_fund_id,$id);
        $this->db->where($this->KEY_datetime,date_format($date, 'Y-m-d H:i:s'));
        $q = $this->db->get($this->Table_name_price);
        if ( $q->num_rows() == 0 ) {
            $data = array(
               $this->KEY_price_fund_id => $id,
               $this->KEY_price => $price ,
               $this->KEY_datetime => date_format($date, 'Y-m-d H:i:s')
            );

            $this->db->insert($this->Table_name_price, $data); 
        }

    }
    
    
}

/* end of file fund_model.php */