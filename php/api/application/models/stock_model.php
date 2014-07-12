<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Funds
*
 * @author ngyikwai
 */
class Stock_model extends CI_Model{

    var $KEY_stock_id = 'code';
    var $KEY_name = 'name';
    var $KEY_link = 'link';
    var $KEY_remark = 'remark';
    var $KEY_valid = 'valid';
    var $Table_name_stock = 'Stock';


    var $Table_name_price = 'StockPrice';
    var $KEY_price_stock_id = 'sid';
    var $KEY_price = 'price';
    var $KEY_datetime = 'datetime';

    //stock info
    var $KEY_vol = 'vol';
    var $KEY_lot_size = 'lot_size';
    var $KEY_turnover = 'turnover';
    var $KEY_52week_low = '52w_low';
    var $KEY_52week_high = '52w_high';
    var $KEY_daily_low = 'daily_low';
    var $KEY_daily_high = 'daily_high';
    var $KEY_mkt_capital = 'mkt_cap';
    var $KEY_PE_Ratio = 'pe_ratio';
    var $KEY_EPS = 'eps';
    var $KEY_yield = 'yield';
    var $KEY_previous_close = 'prev_close';

    function __construct() {
        parent::__construct();
    }
    
    
    /*
     * 
     */
    /*public function get_max_date_with_id_array($requiredFundIDArray){
        $this->db->or_where_in($this->KEY_price_fund_id,$requiredFundIDArray);
        $this->db->select_max($this->KEY_datetime);
        return $this->db->get($this->Table_name_price)->result();
    }

    public function get_min_date_with_id_array($requiredFundIDArray){
        $this->db->or_where_in($this->KEY_price_fund_id,$requiredFundIDArray);
        $this->db->select_min($this->KEY_datetime);
        return $this->db->get($this->Table_name_price)->result();
    }*/

    public function get_max_date(){
        $this->db->select_max($this->KEY_datetime);
        return $this->db->get($this->Table_name_price)->result();
    }

    public function get_min_date(){
        $this->db->select_min($this->KEY_datetime);
        return $this->db->get($this->Table_name_price)->result();
    }

    public function get_all_stocks(){
        $this->db->where($this->KEY_valid,1);
        return $this->db->get($this->Table_name_stock)->result_array();
    }

/*
    public function get_funds_with_id_array($requiredFundIDArray){
        $this->db->or_where_in($this->KEY_fund_id,$requiredFundIDArray);
        $this->db->where($this->KEY_valid,1);
        return $this->db->get($this->Table_name_fund)->result_array();
    }*/
   

    public function get_all_prices(){
        $this->db->order_by($this->KEY_datetime, "desc"); 
        return $this->db->get($this->Table_name_price)->result_array();
    }

    public function get_stock_price_by_id($id){
        $this->db->order_by($this->KEY_datetime, "desc"); 
        $this->db->select($this->KEY_price.",".$this->KEY_datetime);
        $this->db->where($this->KEY_price_stock_id,$id);
        return $this->db->get($this->Table_name_price)->result_array();
    }

    public function insert_stock_price($id, $price,$date_str){
        $date = new DateTime($date_str);
        $this->db->where($this->KEY_price_stock_id,$id);
        $this->db->where($this->KEY_datetime,date_format($date, 'Y-m-d H:i:s'));
        $q = $this->db->get($this->Table_name_price);
        if ( $q->num_rows() == 0 ) {
            $data = array(
               $this->KEY_price_stock_id => $id,
               $this->KEY_price => $price ,
               $this->KEY_datetime => date_format($date, 'Y-m-d H:i:s')
            );

            $this->db->insert($this->Table_name_price, $data); 
        }

    }

    public function update_stock_info($id,$data){
        $this->db->where($this->KEY_stock_id, $id);
        $this->db->update($this->Table_name_stock, $data); 
    }
    
    
}

/* end of file stock_model.php */