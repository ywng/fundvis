  $(function() {

    var slider=$('#ex1').slider({});

    //set slider width (for selecting base price date)
    //invisible on init, it is visible on during percent mode
    $("#ex1Slider").attr("style","width: 950px;display:none");

    $("#ex1").on('slideStop', function(slideEvt) {
      //console.log(slideEvt.value);
      //var index=findIndexGivenDateTime(slideEvt.value,funds_actual[0].price_array);
      //console.log(index);

      //update funds_percent and redraw it
      for(var i=0;i<funds_percent.length;i++){

        var basePriceIndex=findIndexGivenDateTime(slideEvt.value,funds_actual[i].price_array);
        var basePrice=parseFloat(funds_actual[i].price_array[basePriceIndex].price);

        for(var j=0;j<funds_actual[i].price_array.length;j++){
            var percentageChange=((parseFloat(funds_actual[i].price_array[j].price)/basePrice)-1)*100;
            funds_percent[i].price_array[j].price=percentageChange.toString();
          
        }

      }

      update(funds_percent);

    });



    update(funds_actual);

    $("[name='my-checkbox']").bootstrapSwitch();
    $('input[name="my-checkbox"]').on('switchChange.bootstrapSwitch', function(event, state) {
      //console.log(this); // DOM element
      //console.log(event); // jQuery event
      //console.log(state); // true | false

      if(state==true){//state true means that $ is selected , else % is selected
        //console.log("actual price is selected to display");
      
        $("#ex1Slider").attr("style","width: 950px;display:none");

        focus.select(".y.axis").select(".yaxisLabel").transition()
        .text("Actual Price $");//update axis label
    
      update(funds_actual);

      }else{
        
        $("#ex1Slider").attr("style","width: 950px");


        focus.select(".y.axis").select(".yaxisLabel").transition()
        .text("Percentage Change %");//update axis label

      funds_percent=new Array();
      var sliderXPos=slider.data('slider').getValue();

      for(var i=0;i<funds_actual.length;i++){
        var fundObj={
          id:funds_actual[i].id,
          name:funds_actual[i].name,
          price_array:new Array(),
          vis:funds_actual[i].vis
        }

        funds_percent.push(fundObj);

        var basePriceIndex=findIndexGivenDateTime(sliderXPos,funds_actual[i].price_array);
        var basePrice=parseFloat(funds_actual[i].price_array[basePriceIndex].price);

        for(var j=0;j<funds_actual[i].price_array.length;j++){
          var percentageChange=((parseFloat(funds_actual[i].price_array[j].price)/basePrice)-1)*100;
          var priceObj={
            datetime:funds_actual[i].price_array[j].datetime,
            price:percentageChange.toString()
          };
          funds_percent[i].price_array.push(priceObj);
        }
      }

      //update the funds path
      //console.log(funds_percent);
      //console.log(funds_actual);
      update(funds_percent);

      }


    });
    
  });

  
  _init_funds(); 
  var margin = {top: 15, right: 50, bottom: 100, left: 50},
    margin2 = {top: 515, right: 50, bottom: 50, left: 50},
    width = 1200 - margin.left - margin.right,
    height = 570 - margin.top - margin.bottom,
    height2 = 585 - margin2.top - margin2.bottom;
  
  var x = d3.time.scale()
    .range([0, width-150]),
    x2 = d3.time.scale()      //for the time selection bar at the bottom
    .range([0, width-150]);

  var y = d3.scale.linear()
    .range([height, 0]),
    y2 = d3.scale.linear()   //for the time selection bar at the bottom
    .range([height2, 0]);

  var xAxis = d3.svg.axis()
    .scale(x)
    .orient("bottom");
    xAxis2 = d3.svg.axis()
    .scale(x2).orient("bottom");

  var yAxis = d3.svg.axis()
    .scale(y)
    .orient("left");

  x.domain([parseDate(minDate),parseDate(maxDate)]).nice(d3.time.day).ticks(d3.time.day, 1); 
  y.domain([find_max_min_selected_funds(funds_actual).min-1,find_max_min_selected_funds(funds_actual).max+1]);
  x2.domain(x.domain());//the domain axis for the bar at the bottom
  
  var line = d3.svg.line()
    .interpolate("linear")
    .x(function(d) { return x(parseDate(d.datetime)); })
    .y(function(d) { return y(parseFloat(d.price));   });

  var svg = d3.select("#graph").append("svg")
    .attr("width", width + margin.left + margin.right)
    .attr("height", height + margin.top + margin.bottom);
  
  //the main graphic component of the plot
  var focus=svg.append("g")
    .attr("transform", "translate(" + margin.left + "," + margin.top + ")");
  
  //x,y-axis for the plot
  focus.append("g")
    .attr("class", "x axis")
    .attr("transform", "translate(0," + height + ")")
    .call(xAxis);

  focus.append("g")
    .attr("class", "y axis")
    .call(yAxis)
    .append("text")
    .attr("class", "yaxisLabel")
    .attr("transform", "rotate(-90)")
    .attr("y", 6)
    .attr("dy", ".71em")
    .style("text-anchor", "end")
    .text("Actual Price $");

  focus.append("text")
    .attr("class", "fundCategory")
    .attr("x", function(d) { return width-100; })
    .attr("y", 65)
    .text( "JPMorgan Funds")
    .attr("font-family", "sans-serif")
    .attr("font-size", "30px")
    .attr("fill", "black");  
  focus.append("text")
    .attr("class", "fundCategory")
    .attr("x", function(d) { return width-100; })
    .attr("y", 275)
    .text( "Fidelity Funds")
    .attr("font-family", "sans-serif")
    .attr("font-size", "30px")
    .attr("fill", "black");  

 
  //**************** Core part visualizing funds' data ******************************//
  //********************************************************************************//  
  var fund;
  function update(data) {

    y.domain([find_max_min_selected_funds(data).min-1,find_max_min_selected_funds(data).max+1]);
    focus.select(".y.axis").call(yAxis);

    focus.selectAll(".fund").remove();

    //curving part of these funds------------------------------------------------------------------------
    fund = focus.selectAll(".fund")
      .data(data)
      .enter().append("g")
      .attr("class", "fund");

    fund.append("path")
      .attr("class", "line")
      .attr("clip-path", "url(#clip)")//use clip path to make irrelevant part invisible
      .attr("d", function(d) { if(d.vis=="True"){return line(d.price_array);} else{ return null;} })
      .style("stroke", function(d) { return colors(d.id-1); });

  
    
    //fund select or dis-select btn
    fund.append("rect")
      .attr("height",10)
      .attr("width", 25)
      .attr("x",function(d) { 
        if(d.id<=13){
          return width-100+((d.id-1)%3)*40;
        }else{
          return width-100+((d.id-14)%3)*40;
        }
        
      })
      .attr("y",function(d) { 
        if(d.id<=13){
          return 80+Math.floor((d.id-1)/3)*35;
        }else{
          return 80+(Math.floor((d.id-14)/3)+2)*35+Math.floor((13-1)/3)*35;
        }
        
      })
      .attr("stroke", function(d) {return colors(d.id-1);})
      .attr("fill",function(d) {if(d.vis=="True"){return colors(d.id-1);}else{return "white";}})
      .on("click", function(d) { 
            console.log(d.vis);
            if(d.vis=="True"){ //show the curve or not 
              d.vis="False";
            }
            else{
              
              //this enforce that user can select one fund to display when showing actual price
              //because the actual price is of different range, hard or almost impossible to show all the curve meaningfully at the same time

              //but when we use percent based mode, the price unit effect is elminated
              //so, we enable to display all the funds at the same time within same plot axis
              if(data==funds_actual){ 
                for(var i=0;i<funds_actual.length;i++){
                  funds_actual[i].vis="False";
                }
              }

              d.vis="True"; 
            } 

            y.domain([find_max_min_selected_funds(data).min-1,find_max_min_selected_funds(data).max+1]);
            focus.select(".y.axis").call(yAxis);
            
            fund.select("path").transition()//update curve 
              .attr("d", function(d) { if(d.vis=="True"){return line(d.price_array);} else{ return null;} });
          
            fund.select("rect").transition()//update legend 
              .attr("fill",function(d) {if(d.vis=="True"){return colors(d.id-1);}else{return "white";}});
        });  


    var trackingDots =fund.append("circle")
                          .style("stroke", colors(d.id-1))
                          .style("fill", colors(d.id-1))
                          .attr("r", 40)
                          .attr("cx", 50)
                          .attr("cy", 20)
                          .style("opacity", function(d) {if(d.vis=="True"){return 0.1;}else{return 1;}}); 


    //end of curving part of those funds------------------------------------------------------------- 

  }
  //********************************************************************************// 
  //**************** end of Core part visualizing funds' data **********************// 
  

      
  //**************** Slider part & Brush *******************************************//
  //********************************************************************************//
  var brush = d3.svg.brush()//for slider bar at the bottom
    .x(x2)
    .on("brush", brushed);
  
  var context = svg.append("g")
    .attr("transform", "translate(" + margin2.left + "," + margin2.top + ")");
  
  //append click path for controlling the sliding of curve, clip those part out of bounds
  svg.append("defs").append("clipPath") 
  .attr("id", "clip")
  .append("rect")
  .attr("width", width-150)
  .attr("height", height); 
  
  context.append("g")
      .attr("class", "x axis")
      .attr("transform", "translate(0," + height2 + ")")
      .call(xAxis2);
    
  var contextArea = d3.svg.area()
    .interpolate("monotone")
    .x(function(d) { return x2(parseDate(d.datetime)); })
    .y0(height2)//the height is always max height, just to plot a rectangle rather than an actual path of the data
    .y1(0);
  
  //plot the rect as the bar at the bottom
  var dummyObjToIncludeMindate={
    datetime:minDate,
    price:"0"
  };
  var dummyObjToIncludeMaxdate={
    datetime:maxDate,
    price:"0"
  };
  var sampleFundPriceArray=funds_actual[0].price_array.slice();
  sampleFundPriceArray.push(dummyObjToIncludeMaxdate);
  sampleFundPriceArray.push(dummyObjToIncludeMindate);

  context.append("path")
    .attr("class", "area")
    .attr("d", contextArea(sampleFundPriceArray))
    .attr("fill", "LightYellow ");
    
  //append the brush for the selection of subsection  
  context.append("g")
    .attr("class", "x brush")
    .call(brush)
    .selectAll("rect")
    .attr("height", height2)
      .attr("fill", "SkyBlue");  

  //for brusher of the slider bar at the bottom
  function brushed() {
    x.domain(brush.empty() ? x2.domain() : brush.extent());
    fund.select("path").transition()//update curve 
      .attr("d", function(d) { if(d.vis=="True"){return line(d.price_array);} else{ return null;} })
    focus.select(".x.axis").call(xAxis);
  }

  //********************************************************************************//
  //**************End of Slider part & Brush****************************************//

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




    //mouse picker related, hover line---------------------------------------------------------------
  var pickerValue = focus.selectAll(".pickerValue")//for displaying fund unit price 
    .data(funds_actual)
    .enter().append("g")
    .attr("class", "pickerValue");
    
    

        
  var DateLbl = focus.append("g")  //the date label at the right upper corner part
    .attr("class", "dateLabel");

 
  
  //mousePickerDate=getValueForPositionXFromData(width-210);//initial pick date is the last day of the plot
  //for displaying fund unit price
  pickerValue.append("text")
    .attr("class", "valuesLabel")
    .attr("x", function(d) { return width-205; })
    .attr("y", function(d) { return d.id*35+15;})//return getFundID(d.name)*35+15; })
    .text( function (d) { 
      var dateStr="";
      //console.log(mousePickerDate);
    /*  dateStr+=mousePickerDate.getFullYear();
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
      //console.log(dateStr);
      currIndex=DateMapIndex.get(dateStr);
      return d.priceList[currIndex].price; */
    })
           .attr("font-family", "sans-serif")
           .attr("font-size", "15px")
           .attr("fill", function(d) { return colors(d.name); });  
       
 

  //end of mouse picker related, hover line-------------------------------------------------------------------