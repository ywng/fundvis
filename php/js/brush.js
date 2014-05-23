//for brusher of the slider bar at the bottom

function brushed() {
	x.domain(brush.empty() ? x2.domain() : brush.extent());
	fund.select("path").transition()//update curve 
		.attr("d", function(d) { if(d.vis=="1"){return line(d.priceList);} else{ return null;} })
	focus.select(".x.axis").call(xAxis);
}

//this function is mainly for accessing the colors
function getFundID(fundName){
	for(var i=0; i < data2.length; i++) {
		if(data2[i].name==fundName)
		  return i;
	}
}