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
 
//The main data array of the graph
//format:
//data2 (List of fundObj)
//   fundObj (Name, priceList)
//      Name
//      priceList (List of priceItem)
//         priceItem (date, price)
var data2;

var parseDate = d3.time.format("%Y%m%d").parse;
var fundName=new Array();
var DateMapIndex=d3.map();

function _init(){
	// fetch data from database
	//should have some sort of API for getting the data
	$.ajax({
		url: serverDomain+"api/fund/getAllPrice",
		context: document.body,
		dataType: "json", 
		headers : {Accept : "application/json","Access-Control-Allow-Origin" : "*"},
		type: 'GET', 
		async: false,
		success: function(data, textStatus, jqXHR){
			data2=new Array();
			for(var i=0; i < data.length; i++) {
				var fund=new Object();
				fund.vis="0";
				fund.name=data[i][0].name;
				fundName[i]=fund.name;
				fund.priceList=new Array();
				var fundData = new Array();
				fundData=data[i][1].data;
				for(var j=0;j<fundData.length;j++){
					var dailyPrice=new Object();
					dailyPrice.price=parseFloat(fundData[j].price);
					var Str="";
					Str+=fundData[j].year;
					if(fundData[j].month<10){
						Str+="0"+fundData[j].month;
					}else{
						Str+=fundData[j].month;
					}
					if(fundData[j].day<10){
						Str+="0"+fundData[j].day;
					}else{
						Str+=fundData[j].day;
					}
					
					//construct the map for mapping date to array index
					//only do it once for first series is okay
					if(i==0){
						DateMapIndex.set(Str,j);
					}
					
					dailyPrice.date= parseDate(Str);
					fund.priceList[j]=dailyPrice;
				}
				data2[i]=fund;
			}
			data2[0].vis="1";//when start, only visible the first one, other let user set it.	
		},
		error: function(jqHXR, textStatus, errorThrown) {
			console.log('ajax error in get survey ID call:' +textStatus + ' ' + errorThrown);
		}

	 }); // end of the ajax call

}

function findMaxY(){
	var max=-9999999;
	for(var i=0; i < data2.length; i++) {
		if(data2[i].vis=="1"){//only find within those selected fund sets
			var fundData=data2[i].priceList;		
			for(var j=0;j<fundData.length;j++){
				if(fundData[j].price>max){
					max=fundData[j].price;
				}
			}
		}
	}
	return max;
}

function findMinY(){
	var min=9999999;
	for(var i=0; i < data2.length; i++) {
		if(data2[i].vis=="1"){
			var fundData=data2[i].priceList;		
			for(var j=0;j<fundData.length;j++){
				if(fundData[j].price<min){
					min=fundData[j].price;
				}
			}
		}
	}
	return min;
}
