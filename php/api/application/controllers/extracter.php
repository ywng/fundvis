<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/**
 * Extract -- Web Scrapping
 */

require_once (APPPATH. 'libraries/REST_Controller.php');

class Extracter extends REST_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->library('CORE_Controller');
		$this->load->library('simple_html_dom');
		$this->load->helper(array('form', 'url'));
		$this->core_controller->set_response_helper($this);
	
	}

	var $user_type = '';


	public function extract_get(){

		$this->load->model('fund_model'); 
		$funds = $this->fund_model->get_all_funds();

		foreach ($funds as $fund){

			// Create DOM from URL or file
			$html = file_get_html($fund[$this->fund_model->KEY_link]);
			if (strlen(strstr($fund[$this->fund_model->KEY_link],"http://www.jpmorganam.com.hk/jpm/am/"))>0) {
				// JPM webpages
				$this->JPM_extract($html,$fund);

			}else if (strlen(strstr($fund[$this->fund_model->KEY_link],"http://www.bloomberg.com/quote/"))>0){
				// Bloomberg webpages
				$this->bloomberg_extract($html,$fund);
			}

		}

		$this->core_controller->successfully_processed();

	}

	public function stock_extract_in_any_portfolio_get(){

		$this->load->model('portfolio_model'); 
		$this->load->model('stock_model'); 
		$stocks = $this->portfolio_model->get_all_stock_code_in_any_portfolio();
		//var_dump($stocks);
		$this->stock_extract_given_stocks($stocks);

	}

	public function stock_extract_batch1_get(){

		$this->load->model('stock_model'); 
		$stocks = $this->stock_model->get_all_stocks_batch1();

		$this->stock_extract_given_stocks($stocks);

	}
	public function stock_extract_batch2_get(){
		$this->load->model('stock_model'); 
		$stocks = $this->stock_model->get_all_stocks_batch2();

		$this->stock_extract_given_stocks($stocks);
	}

	public function stock_extract_batch3_get(){
		$this->load->model('stock_model'); 
		$stocks = $this->stock_model->get_all_stocks_batch3();

		$this->stock_extract_given_stocks($stocks);
	}

	public function AASTOCK_stock_getinfo($stock_code){
		$html = file_get_html("http://www.aastocks.com/en/ltp/rtquote.aspx?symbol=".$stock_code);
		if (strlen(strstr($html->plaintext,"Sorry, stock code "))>0) {
				// Stock code not found!!!
				$this->core_controller->fail_response(101);

		}else{
			//name
			$name_e=$html->find('title')[0];
			$name=explode("&nbsp;",$name_e->plaintext)[0];
			$name=trim($name);
			//var_dump($name);
			$category_e=$html->find('div[id=indDet]')[0];
			$category=trim($category_e->plaintext);
			//var_dump($category);
			$data= array(
				"name"=>$name,
				"category"=>$category,

			);

			return $data;
		}

	}

	public function AASTOCK_fetch_stock_name_Chinese_get(){
		$this->load->model('stock_model'); 
		$stocks = $this->stock_model->get_all_stocks();

		foreach ($stocks as $stock){

			$html_tc = file_get_html("http://www.aastocks.com/tc/LTP/RTQuote.aspx?symbol=".$stock[$this->stock_model->KEY_stock_id]);
			$html_en = file_get_html("http://www.aastocks.com/en/ltp/rtquote.aspx?symbol=".$stock[$this->stock_model->KEY_stock_id]);
			if (strlen(strstr($html_tc->plaintext,"Sorry, stock code "))>0||strlen(strstr($html_en->plaintext,"Sorry, stock code "))>0) {
					// Stock code not found!!!
					$this->core_controller->fail_response(101);

			}else{
				//name en & tc
				$name_en_e=$html_en->find('title')[0];
				$name_en=explode("&nbsp;",$name_en_e->plaintext)[0];
				$name_en=trim($name_en);

				$name_tc_e=$html_tc->find('title')[0];
				$name_tc=explode("&nbsp;",$name_tc_e->plaintext)[0];
				$name_tc=trim($name_tc);

				$combined_name;
				$name_en_for_compare=preg_replace("/[^0-9.a-zA-Z]/", '',$name_en);
				$name_tc_for_compare=preg_replace("/[^0-9.a-zA-Z]/", '',$name_tc);
				if($name_en_for_compare==$name_tc_for_compare){
						$combined_name=$name_en; 
				}else{
						$combined_name=$name_tc." ".$name_en;
				}
			
				$data= array(
					$this->stock_model->KEY_name =>$combined_name,//record both Eng & Trand Chinese name

				);
				$this->stock_model->update_stock_info($stock[$this->stock_model->KEY_stock_id],$data);
			}
		}

	}

	//private helpers
	//not open APIs
	//==========================================================================================//

	private function AASTOCK_stock_extract($html,$stock){
		//go to the data section
		//log 
		//var_dump($stock);

		$data_table=$html->find('table[class=tb-c]')[0];
		//var_dump($data_table);
		try {
			// price span
			$price_e_array=$data_table->find('div[class=C font28 C bold]');
		}
		// if the price is not positive
		catch(Exception $e) {
		  	echo 'Message: ' .$e->getMessage();
		}
	
		if(!$price_e_array){
			echo "action:stockExtraction, stage=getPriceTag message: Extraction failed for stock: ".$stock[$this->stock_model->KEY_stock_id];
			return;//invalid stock code..

		}else{
			$price_e=$price_e_array[0];
			$price_chg=0;
			//$price_chg_e=$price_e_array[1];
			//$price_chg=(float)preg_replace("/[^0-9.]/", '',$price_chg_e->plaintext);
		}
		
		$price=(float)preg_replace("/[^0-9.]/", '',$price_e->plaintext);

		//date span
		$datetime_e=$html->find('div[style=font-size: 10px;]')[0];
		if(!$datetime_e){
			echo "action:stockExtraction, stage=getDateTimeTag message: Extraction failed for stock: ".$stock[$this->stock_model->KEY_stock_id];
			return;
		}
		if (strlen(strstr($datetime_e->plaintext,"Suspension"))>0 || strlen(strstr($datetime_e->plaintext,"暫停買賣"))>0){
			return null;
		}
		var_dump($datetime_e->plaintext);
		if (preg_match('/(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2})/',$datetime_e->plaintext, $regs)) {
			$datetime_str = $regs[0];
	    } 

	    if(!$price || !$datetime_str){
	    	$this->core_controller->fail_response(100);
	    }

	    //daily range
		$daily_range_e=$html->find('strong')[0];
		$daily_range=explode("-",$daily_range_e->plaintext);
		var_dump($daily_range);

	    //vol
		$vol_e=$html->find('strong')[1];
		$vol=preg_replace("/[^0-9.a-zA-Z]/", '',$vol_e->plaintext);
		//var_dump($vol);

		//mkt cap
		$mkt_cap_e=$html->find('strong')[2];
		$mkt_cap=preg_replace("/[^0-9.a-zA-Z]/", '',$mkt_cap_e->plaintext);

		//turnover
		$turnover_e=$html->find('strong')[3];
		$turnover=preg_replace("/[^0-9.a-zA-Z]/", '',$turnover_e->plaintext);

		//EPS
		$EPS_e=$html->find('strong')[4];
		$EPS=preg_replace("/[^0-9.]/", '',$EPS_e->plaintext);

		//PE Ratio
		$PE_Ratio_e=$html->find('strong')[5];
		$PE_Ratio=preg_replace("/[^0-9.]/", '',$PE_Ratio_e->plaintext);

		//Yield
		$yield_e=$html->find('strong')[6];
		$yield=preg_replace("/[^0-9.]/", '',$yield_e->plaintext);

		//Lot size
		$lot_size_e=$html->find('strong')[7];
		$lot_size=preg_replace("/[^0-9]/", '',$lot_size_e->plaintext);

		//52 week range
		$week52_range_e=$html->find('strong')[8];
		$week52_range=explode("-",$week52_range_e->plaintext);

		$stock_updated_info = array(
			  $this->stock_model->KEY_daily_low => preg_replace("/[^0-9.]/", '',$daily_range[0]),
			  $this->stock_model->KEY_daily_high => preg_replace("/[^0-9.]/", '',$daily_range[1]),
			  $this->stock_model->KEY_vol => $vol,
			  $this->stock_model->KEY_mkt_capital => $mkt_cap,
			  $this->stock_model->KEY_turnover => $turnover,
			  $this->stock_model->KEY_EPS => $EPS,
			  $this->stock_model->KEY_PE_Ratio => $PE_Ratio,
			  $this->stock_model->KEY_yield => $yield,
			  $this->stock_model->KEY_lot_size => $lot_size,
			  $this->stock_model->KEY_52week_low => preg_replace("/[^0-9.]/", '',$week52_range[0]),
			  $this->stock_model->KEY_52week_high =>  preg_replace("/[^0-9.]/", '',$week52_range[1]),

			  $this->stock_model->KEY_previous_close => ($price+$price_chg),
		);
		var_dump($stock_updated_info);
		$this->core_controller->add_return_data($stock[$this->stock_model->KEY_stock_id].".info",$stock_updated_info); 
		$this->stock_model->update_stock_info($stock[$this->stock_model->KEY_stock_id],$stock_updated_info);


	    $stock_price = array(
               $this->stock_model->KEY_name => $stock[$this->stock_model->KEY_name],
               $this->stock_model->KEY_price => $price ,
               $this->stock_model->KEY_datetime => $datetime_str
        );
        $this->core_controller->add_return_data($stock[$this->stock_model->KEY_stock_id].".price",$stock_price); 
	    $this->stock_model->insert_stock_price($stock[$this->stock_model->KEY_stock_id],$price,$datetime_str);

	}
	

	private function bloomberg_extract($html,$fund){
		// price span
		$price_e=$html->find('div[class=price]')[0];
		
		$price=preg_replace("/[^0-9.]/", '',$price_e->plaintext);

		//date span
		$date_e=$html->find('div[class=price-datetime]')[0];
		//var_dump($date_e);
		/*if (preg_match('/(0[1-9]|1[012])[\/](0[1-9]|[12][0-9]|3[01])[\/](19|20)[0-9]{2}/',$date_e->plaintext, $regs)) {
			$date_str = $regs[0];
	    } */
	    $date_str =explode(" EST ", $date_e->plaintext)[1];

	    if($price==null || $date_str==null)
	    	return; //invalid values, skip insert

	    $fund_daily_price = array(
               $this->fund_model->KEY_name => $fund[$this->fund_model->KEY_name],
               $this->fund_model->KEY_price => $price ,
               $this->fund_model->KEY_datetime => $date_str
        );
        $this->core_controller->add_return_data($fund[$this->fund_model->KEY_fund_id],$fund_daily_price); 

	    $this->fund_model->insert_fund_daily_price($fund[$this->fund_model->KEY_fund_id],$price,$date_str);
	}

	private function JPM_extract($html,$fund){

		$element_div=$html->find('div[class=daily_price_box]')[0];
		$raw_str=$element_div->children(1)->children(0)->children(0)->plaintext;
		//var_dump($raw_str);
		$data=explode(" ",$raw_str);
		$raw_price=null;
		$raw_date=null;
		for($i=0;$i<count($data);$i++){
			if (preg_match('/[A-Za-z]/', $data[$i]) || preg_match('/[0-9]/', $data[$i]))
			{
			    if($raw_date==null){
			    	$raw_date=$data[$i];
			    }else{
			    	$raw_price=$data[$i];
			    }
			}
		}

		$price=preg_replace("/[\t]/", '',$raw_price);
		$date_str=preg_replace("/[\t]/", '',$raw_date);
		$date_arr=explode(".",$date_str);
		$date_str="20".$date_arr[2]."-".$date_arr[1]."-".$date_arr[0];

		if($price==null || $date_str==null)
	    	return; //invalid values, skip insert

 		$fund_daily_price = array(
               $this->fund_model->KEY_name => $fund[$this->fund_model->KEY_name],
               $this->fund_model->KEY_price => $price ,
               $this->fund_model->KEY_datetime => $date_str
        );
        $this->core_controller->add_return_data($fund[$this->fund_model->KEY_fund_id],$fund_daily_price); 


		$this->fund_model->insert_fund_daily_price($fund[$this->fund_model->KEY_fund_id],$price,$date_str);
	}

	private function stock_extract_given_stocks($stocks){
		foreach ($stocks as $stock){

			// Create DOM from URL or file
			$html = file_get_html($stock[$this->stock_model->KEY_link]);
			if (strlen(strstr($stock[$this->stock_model->KEY_link],"http://www.aastocks.com/"))>0) {
				// AASTOCK webpages
				$this->AASTOCK_stock_extract($html,$stock);

			}

		}

		$this->core_controller->successfully_processed();
	}
	

}

/* End of file extracter.php */
/* Location: ./application/controllers/extracter.php */