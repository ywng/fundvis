<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Notifications
*
 * @author ngyikwai
 */
class Notification_model extends CI_Model{

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
    
   
    //====================================================//
   




}

/* end of file notification_model.php */