<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Funds
*
 * @author ngyikwai
 */
class Fund_model extends CI_Model{

    var $KEY_id = 'id';
    var $KEY_name = 'name';
    var $KEY_link = 'link';
    var $KEY_remark = 'remark';
    var $Table_name_fund = 'Fund';
    var $Table_name_price = 'Price';
    

    function __construct() {
        parent::__construct();
    }
    
    
    /*
     * 
     */

    public function getAllFunds(){
        return $this->db->get($this->Table_name_fund)->result_array();
    }

    public function insert_fund_daily_price($price,$date){


    }
    
    
}

/* end of file fund_model.php */