/**
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 * http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
 
 /**
 * Author: NG, Yik-wai Jason
 * Contact & Support: ywng@ust.hk
 * The Hong Kong University of Science and Technology
 * Data Visualization, CSE, HKUST
 */
 

 /**
  * Fund Data Related functions
  *
  */

var parseDate = d3.time.format("%Y-%m-%d %H:%M:%S").parse;
var funds=new Array();

function _init_funds(){
	// fetch data from database
	$.ajax({
		url: serverDomain+"api/fund/getAllPrice",
		context: document.body,
		dataType: "json", 
		headers : {Accept : "application/json","Access-Control-Allow-Origin" : "*"},
		type: 'GET', 
		async: false,
		success: function(data, textStatus, jqXHR){
			funds=data.funds;
			for(var i=0; i < funds.length; i++) {


				/*var fund=new Object();
				fund.vis="0";
				fund.name=funds_arr[i].name;
				fundName[funds_arr[i].id]=fund.name;
				fund.priceList=new Array();
			
				for(var j=0;j<funds_arr[i].price_array.length;j++){
					var dailyPrice=new Object();
					dailyPrice.price=parseFloat(funds_arr[i].price_array[j].price);
					
					
					//construct the map for mapping date to array index
					//only do it once for first series is okay
					if(i==0){
						DateMapIndex.set(funds_arr[i].price_array[j].datetime,j);
					}
					
					dailyPrice.date= parseDate(funds_arr[i].price_array[j].datetime);
					fund.priceList[j]=dailyPrice;
				}
				data2[i]=fund;*/
			}
			funds[0].vis="True";//when start, only visible the first one, other let user set it.	
		},
		error: function(jqHXR, textStatus, errorThrown) {
			console.log('ajax error in get survey ID call:' +textStatus + ' ' + errorThrown);
		}

	 }); // end of the ajax call

}

