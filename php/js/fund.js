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
var maxDate;
var minDate;

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
			maxDate=data.max_date;
			minDate=data.min_date;
			for(var i=0; i < funds.length; i++) {


				/*

					
					//construct the map for mapping date to array index
					//only do it once for first series is okay
					if(i==0){
						DateMapIndex.set(funds_arr[i].price_array[j].datetime,j);
					}
				
				*/
			}
			funds[0].vis="True";//when start, only visible the first one, other funds, let user set it later.	
		},
		error: function(jqHXR, textStatus, errorThrown) {
			console.log('ajax error in get survey ID call:' +textStatus + ' ' + errorThrown);
		}

	 }); // end of the ajax call

}

