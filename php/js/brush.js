//for slider part-----------------------------------------------------------------------------------
	var brush = d3.svg.brush()//for slider bar at the bottom
    .x(x2)
    .on("brush", brushed);
	
	var context = svg.append("g")
    .attr("transform", "translate(" + margin2.left + "," + margin2.top + ")");
	
	//append click path for controlling the sliding of curve, clip those part out of bounds
	svg.append("defs").append("clipPath") 
	.attr("id", "clip")
	.append("rect")
	.attr("width", width-210)
	.attr("height", height); 
  
	context.append("g")
      .attr("class", "x axis")
      .attr("transform", "translate(0," + height2 + ")")
      .call(xAxis2);
	  
	var contextArea = d3.svg.area()
		.interpolate("monotone")
		.x(function(d) { return x2(d.date); })
		.y0(height2)
		.y1(0);
	
	//plot the rect as the bar at the bottom
	context.append("path")
		.attr("class", "area")
		.attr("d", contextArea(data2[0].priceList))
		.attr("fill", "LightYellow ");
		
	//append the brush for the selection of subsection  
	context.append("g")
		.attr("class", "x brush")
		.call(brush)
		.selectAll("rect")
		.attr("height", height2)
	    .attr("fill", "SkyBlue");  
	//end slider part-----------------------------------------------------------------------------------



//for brusher of the slider bar at the bottom

function brushed() {
	x.domain(brush.empty() ? x2.domain() : brush.extent());
	fund.select("path").transition()//update curve 
		.attr("d", function(d) { if(d.vis=="True"){return line(d.price_array);} else{ return null;} })
	focus.select(".x.axis").call(xAxis);
}

//this function is mainly for accessing the colors
function getFundID(fundName){
	for(var i=0; i < data2.length; i++) {
		if(data2[i].name==fundName)
		  return i;
	}
}