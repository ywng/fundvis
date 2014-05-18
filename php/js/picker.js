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
	 * Mouse Picker related functions
	 */
	var handleMouseOverGraph = function(event) {	
		var mouseX = event.pageX-90;
		var mouseY = event.pageY-34;
	
		if(mouseX >= 0 && mouseX <=990 && mouseY >= 0 && mouseY <= 500) {
			//console.log(mouseX+"  "+mouseY);
			// show the hover line
			hoverLineGroup.select('line').remove();
			hoverLineGroup.append("line")
				.attr("x1", mouseX).attr("x2", mouseX) 
				.attr("y1", 0).attr("y2", height) 
				.style("stroke", "DarkViolet")
				.style("stroke-width", 0.2);
			//update date label
			displayDateForPositionX(mouseX);
		} else {
			//out of the bounds that we want
			handleMouseOutGraph(event);
		}
	}
	
	
	var handleMouseOutGraph = function(event) {	
		// hide the hover-line
		hoverLineGroup.select('line').remove();
		
		//Set the value labels to whatever the latest data point is.
		//when the user is not scanning through the graph
		displayDateForPositionX(width-210);
	}
	
	/**
	* Display the data & date values at position X 
	*/
	var displayDateForPositionX = function(xPosition) {
		//console.log("xPos:"+xPosition);
		var dateToShow=getValueForPositionXFromData(xPosition);
		mousePickerDate=dateToShow;
		DateLbl.select('text').remove();
		DateLbl.append("text")
			.attr("x",width-550)
            .attr("y", 0)
			.text(dateToShow)
            .attr("font-family", "sans-serif")
            .attr("font-size", "10px")
            .attr("fill", "Gray");
	
		//recalculate the current index where the hover lines is on
		var dateStr="";
		//console.log(mousePickerDate);
		dateStr+=mousePickerDate.getFullYear();
		if(mousePickerDate.getMonth()+1<10){
			dateStr+="0"+(mousePickerDate.getMonth()+1);
		}else{
			dateStr+=(mousePickerDate.getMonth()+1);
		}
		if(mousePickerDate.getDate()<10){
			dateStr+="0"+mousePickerDate.getDate();
		}else{
			dateStr+=mousePickerDate.getDate();
		}
		currIndex=DateMapIndex.get(dateStr);//update current index
		//console.log("date:"+dateStr+" index:"+currIndex);
		
		//modify the picker display of each funds	
		//do only when we have a defined update of index. For some of the date, e.g. Sunday, no such record, the index will be undefined
		if((currIndex>=0 )&& (currIndex<data2[0].priceList.length)){ 
		
			pickerValue.select("text").transition()//update the unit price label 
				.text( function (d) { 
					return d.priceList[currIndex].price;
				});
			
			valueChange.select("text").transition()//update % value change label
				.text( function (d) {
					var percentChange;				
					if(currIndex==d.priceList.length-1)
						percentChange=(d.priceList[currIndex].price-d.priceList[currIndex-1].price)/d.priceList[currIndex-1].price*100;
					else
					    percentChange=(d.priceList[d.priceList.length-1].price-d.priceList[currIndex].price)/d.priceList[currIndex].price*100;
					if(percentChange<0){
						return "(-"+percentChange.toFixed(2)+"%)" 
					}else{
						return "(+"+percentChange.toFixed(2)+"%)" 
					}
				})
				.attr("fill", function (d) {
					var percentChange;				
					if(currIndex==d.priceList.length-1)
						percentChange=(d.priceList[currIndex].price-d.priceList[currIndex-1].price)/d.priceList[currIndex-1].price*100;
					else
					    percentChange=(d.priceList[d.priceList.length-1].price-d.priceList[currIndex].price)/d.priceList[currIndex].price*100;
					if(percentChange<0){
						return "red" 
					}else{
						return "black" 
					}
				});
		}
		
	}
	
	/**
	* Convert back from an X position on the graph to a data value 
	* 
	*/
	function getValueForPositionXFromData(xPosition) {
		// get the date on x-axis for the current location
		var xValue = x.invert(xPosition);
		//console.log(xValue);
		return xValue;
	}

