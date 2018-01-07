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
var funds_actual=new Array();
var funds_percent;//null initially, will create object when percent mode is triggered
var maxDate;
var minDate;

function _init_funds(url){
	// fetch data from database
	$.ajax({
		url: serverDomain+url,
		context: document.body,
		dataType: "json", 
		headers : {Accept : "application/json","Access-Control-Allow-Origin":"*"},
		type: 'GET', 
		async: false,
		success: function(data, textStatus, jqXHR){
			funds_actual=data.funds;
			maxDate=data.max_date.datetime;
			minDate=data.min_date.datetime;

			for(var i=0; i < funds_actual.length; i++) {
				funds_actual[i].vis="False";
			}

			funds_actual[0].vis="True";//when start, only visible the first one, other funds, let user set it later.	
			
		},
		error: function(jqHXR, textStatus, errorThrown) {
			console.log('ajax error in get survey ID call:' +textStatus + ' ' + errorThrown);
		}

	 }); 


}

function find_max_min_selected_funds(data){
	var max=-999999;
	var min=999999;
	for(var i=0;i<data.length;i++){
		if(data[i].vis=="True"){
			for(var j=0;j<data[i].price_array.length;j++){
				var price=parseFloat(data[i].price_array[j].price);
				if(price>max){
					max=price;
				}
				if(price<min){
					min=price;
				}
			}
		}
		
	}

	var max_min_price={
		max:max,
		min:min
	}
	return max_min_price;
}

function findIndexGivenDateTime(xAxisPos,price_array){
	
	for(var i=price_array.length-1;i>=0;i--){
		var date=parseDate(price_array[i].datetime+" 23:59:59");
		var mousePosDate=x.invert(xAxisPos);

		//because the index doesn't include Sat, Sun or public holiday, *1/2 getting an estimate to speed up the search
		var diff_days=Math.floor((mousePosDate.getTime()-date.getTime())/(1000*60*60*24)*1/2);//in days

		//console.log(date+"  "+mousePosDate+"  "+diff_days);
		/* if the days diff more than 15 days, we fastforward the index i to the destinated index pos */
		if(diff_days>15){ 
			i=i-diff_days;
			continue;
		}
	
		//it will get executed where diff in days <=15, to get more precise position caculation when we are almost there
		var diff_xAxis_pos=xAxisPos-x(date);
		if(diff_xAxis_pos<=0){//it means the date of that index (i) is equal or just greater than the date of the mouse position
			return i;
		}

	}
	return 0; // no date throughout the price array is greater than the selected date, so return the index of the greatest date

}


