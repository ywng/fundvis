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

 	//check the mouse event over the plot area and do the processing 
	container = document.querySelector('#graph');
		$(container).mouseleave(function(event) {
		handleMouseOutGraph(event);
		//console.log("mouse leave");
	})
	$(container).mousemove(function(event) {
		handleMouseOverGraph(event);
		//console.log("mouse move on graph");
	})    
 
	/**
	 * Mouse Picker related functions
	 */
	var handleMouseOverGraph = function(event) {	
		//console.log(event.pageX);
		//console.log(event.pageY);
		var mouseX = event.pageX-90;
		var mouseY = event.pageY-50;
	
		if(mouseX >= 0 && mouseX <=950 && mouseY >= 0 && mouseY <= 460) {
			//console.log(mouseX+"  "+mouseY);

			//show date 
			displayDateForPositionX(mouseX);

			//show dots
			displayDotsForPositionX(mouseX);

			//show price
			displayPricePositionX(mouseX);
			

		} else {
			// hide the dots
			fund.select("circle").transition().duration(0)
		    	.style("display", "none");
		    fund.select("text").transition().duration(0)
		    	.text("");
		    DateLbl.select('text').remove();
	
		}
	}
	
	
	var handleMouseOutGraph = function(event) {	
		// hide the dots
		fund.select("circle").transition().duration(0)
		    .style("display", "none");
		fund.select("text").transition().duration(0)
		    .text("");
		DateLbl.select('text').remove();
	}
	
	/**
	* Display the data & date values at position X 
	*/
	var displayDateForPositionX = function(xPosition) {
		
		var dateToShow=x.invert(xPosition);
		
		DateLbl.select('text').remove();

		DateLbl.append("text")
			.attr("x",width-201)
            .attr("y", 0)
			.text(convertDate(dateToShow))
            .attr("font-family", "sans-serif")
            .attr("font-size", "16px")
            .attr("fill", "Gray");
	}

	/**
	* Display dot at position X 
	*/
	var displayDotsForPositionX = function(xPosition) {
		
		// show the dots
		
		fund.select("circle").transition().duration(0)
			 .attr("cx",  function(d) {
			 	 var index=findIndexGivenDateTime(xPosition,d.price_array);
			 	 if(x(parseDate(d.price_array[index].datetime+" 12:00:00"))>950){
					return -999999; //not display
				 }
			 	 return x(parseDate(d.price_array[index].datetime+" 12:00:00"));
			 })
	         .attr("cy",  function(d) {
	         	 var index=findIndexGivenDateTime(xPosition,d.price_array);
	         	 return y(parseFloat(d.price_array[index].price));
	         })
	         .style("display", function(d) {if(d.vis=="True"){return "initial";}else{return "none";}}); 
	}
	

	/**
	* Display unit price / percent change on position x's date
	*/
	var displayPricePositionX = function(xPosition) {
		

		fund.select("text").transition().duration(0)
			 .text(function(d){
			 	if(d.vis=="True"){
			 		var index=findIndexGivenDateTime(xPosition,d.price_array);

				 	if(mode=="actual"){
				 	 	return parseFloat(d.price_array[index].price);
				 	}else if(mode=="percent"){
				 		return parseFloat(d.price_array[index].price).toFixed(2)+"%";
				 	}
			 	}else{
			 		return "";
			 	}
			 	

			 })
			 .attr("fill", function(d){
			 	var index=findIndexGivenDateTime(xPosition,d.price_array);

			 	if(mode=="actual"){
			 		if(index==(d.price_array.length-1))
			 	 		return "black";
			 	 	if(parseFloat(d.price_array[index].price)>parseFloat(d.price_array[index+1].price)){
			 	 		return "blue";
			 	 	}else if (parseFloat(d.price_array[index].price)==parseFloat(d.price_array[index+1].price)){
			 	 		return "black";
			 	 	}else{
			 	 		return "red";
			 	 	}
			 	}else if(mode=="percent"){
			 		var price=parseFloat(d.price_array[index].price);
			 		if(price>0){
			 			return "blue";
			 		}else if(price==0){
			 			return "black";
			 		}else{
			 			return "red";
			 		}
			 	}

			 }); 

		
	}

	//tooltip callback

	//details var determins whether other fund details are shown
	function showTooltip(d,details,offsetX,offsetY) {
		//console.log("on mouse over: "+d3.event.pageX+"  "+d3.event.pageY);
		div.transition()        
		   .duration(200)      
		   .style("opacity", .9);      
		div.html(d.name)  
		   .style("left", (d3.event.pageX+offsetX) + "px")     
		   .style("top", (d3.event.pageY+offsetY ) + "px");    
	                       
	}

	function hideTooltip() {
		 div.transition()        
		    .duration(500)      
		    .style("opacity", 0);   
	}
  
       
	

