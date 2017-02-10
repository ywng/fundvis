<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Funds
*
 * @author ngyikwai
 */
class Portfolio_model extends CI_Model{

    var $KEY_portfolio_id = 'pid';
    var $KEY_portfolio_stock_id = 'sid';

    var $KEY_stock_code = 'code';
   
    var $Table_name_portfolio = 'Portfolio';
    var $Table_name_user_portfolio = 'UserPortfolio';
    var $Table_name_portfolio_stock = 'PortfolioStock';
    var $Table_name_stock = 'Stock';

    
    function __construct() {
        parent::__construct();
    }
    
    public function get_all_stock_code_in_any_portfolio(){
        $this->db->select('*');
        $this->db->from($this->Table_name_portfolio_stock.' portStock');
        $this->db->join($this->Table_name_stock.' stock', 
            'stock.'.$this->KEY_stock_code.' = '.'portStock.'.$this->KEY_portfolio_stock_id, 'inner');

        $this->db->group_by($this->KEY_portfolio_stock_id);
        return $this->db->get()->result_array();
      
    }

    public function get_portfolio_stock_list($portId){
       
    
    }

    


}

/* end of file portfolio_model.php */