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

    //stock category
    var $KEY_category_id = 'id';
    var $KEY_category= 'category';
    var $Table_name_category = 'StockCategory';

    function __construct() {
        parent::__construct();
    }
    
    //stock related functions
    //====================================================//
    public function get_stock_by_id($code){
        $this->db->where($this->KEY_valid,1);
        $this->db->where($this->KEY_stock_id,$code);
        return $this->db->get($this->Table_name_stock)->result_array();
    }

    public function get_all_stocks(){
        $this->db->where($this->KEY_valid,1);
        return $this->db->get($this->Table_name_stock)->result_array();
    }

    public function get_all_stocks_batch1(){
        $this->db->where($this->KEY_valid,1);
        $this->db->where($this->KEY_stock_id." <=",600);
        return $this->db->get($this->Table_name_stock)->result_array();
    }

    public function get_all_stocks_batch2(){
        $this->db->where($this->KEY_valid,1);
        $this->db->where($this->KEY_stock_id." >",600);
        $this->db->where($this->KEY_stock_id." <=",2400);
        return $this->db->get($this->Table_name_stock)->result_array();
    }

    public function get_all_stocks_batch3(){
        $this->db->where($this->KEY_valid,1);
        $this->db->where($this->KEY_stock_id." >",2400);
        return $this->db->get($this->Table_name_stock)->result_array();
    }

    public function addStock($data){
        $this->db->where($this->KEY_stock_id,$data[$this->KEY_stock_id]);
        $q = $this->db->get($this->Table_name_stock);
        if ( $q->num_rows() == 0 ) {
            $this->db->insert($this->Table_name_stock, $data); 
        }else{
            $this->db->where($this->KEY_stock_id,$data[$this->KEY_stock_id]);
            $this->db->update($this->Table_name_stock, $data);
        }
    }

    public function update_stock_info($id,$data){
        $this->db->where($this->KEY_stock_id, $id);
        $this->db->update($this->Table_name_stock, $data); 
    }



    //stock price related functions
    //====================================================//

    public function get_max_date(){
        $this->db->select_max($this->KEY_datetime);
        return $this->db->get($this->Table_name_price)->result();
    }

    public function get_min_date(){
        $this->db->select_min($this->KEY_datetime);
        return $this->db->get($this->Table_name_price)->result();
    }

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

    public function get_curr_stock_price_by_id($id){
        $this->db->order_by($this->KEY_datetime, "desc"); 
        $this->db->select($this->KEY_price.",".$this->KEY_datetime);
        $this->db->where($this->KEY_price_stock_id,$id);
        
        $q=$this->db->get($this->Table_name_price);
        if( $q->num_rows() >0){
            return $q->result_array()[0];
        } else{
            return null;
        }
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

    //stock category related functions
    //====================================================//

    
    public function getCategoryID($category){
        $this->db->where($this->KEY_category,$category);
        $q = $this->db->get($this->Table_name_category);
        if ( $q->num_rows() == 0 ) {
            $data = array(
               $this->KEY_category => $category 
            );

            $this->db->insert($this->Table_name_category, $data); 
            $this->db->where($this->KEY_category,$category);
            $q = $this->db->get($this->Table_name_category);
        }
        
        return $q->result_array()[0][$this->KEY_category_id];
        
    }




}

/* end of file stock_model.php */