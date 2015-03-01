var stockname="";

$(document).ready(function() {
    //load common side bar & init the side bar by callback function
    $('#nav_bar').load("nav.html",sideBarInit);
    checkRedirectNeeded();

    alertDataTableInit();
    addAlertInit();

    $("#checkAll").click(function () {
        $(".alertCheckBox").prop('checked',$(this).prop('checked'));
    }); 

   $(".alertCheckBox").click(function(){
        if (!$(this).prop('checked')){ // if not check a single box
             $("#checkAll").prop('checked',false); // then select all will be not selected
        }else{                         //if that check box is checked, need to check whether all are checked
            $("#checkAll").prop('checked',true);
            $(".alertCheckBox").each(function(){
                if (!$(this).prop('checked')){
                    $("#checkAll").prop('checked',false);
                }
            });
        }
        
    });

    checkRedirectNeeded();
});


/*
 * Alerts DataTable Related
 */
var alertsTable;
var alerts_arr;
function alertDataTableInit(){

    alertsTable=$('#dataTables-example').dataTable({ //init alerts table after loading the alerts data
        "iDisplayLength": 5,
        "aaData":alerts_arr,
        "aoColumns": [
            {"bSortable": false, "bVisible": false},
            { "bSortable": false, "bSearchable":false, "sWidth": "5px" },
            { "bSortable": true, "bSearchable": true, "sWidth": "50px" },
            { "bSortable": false },
            { "bSortable": false, "bSearchable":true, "sWidth": "145px" },
            { "bSortable": true, "bSearchable": true, "sWidth": "50px" },
            { "bSortable": true, "bSearchable": true, "sWidth": "50px" },
            { "bSortable": true, "bSearchable": true, "iDataSort": 9, "sWidth": "50px" },
            { "bSortable": true, "bSearchable": true, "iDataSort": 10, "sWidth": "140px" },
            {"bVisible": false},
            {"bVisible": false},
        ],
        "fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
            
        },
        "fnDrawCallback": function(){
            doTableUISetUp();

            $("#checkAll").prop('checked',false);
            $(".alertCheckBox").each(function(){
               $(this).prop('checked',false);
            });

            $(".alertCheckBox").click(function(){
                if (!$(this).prop('checked')){ // if not check a single box
                     $("#checkAll").prop('checked',false); // then select all will be not selected
                }else{                         //if that check box is checked, need to check whether all are checked
                    $("#checkAll").prop('checked',true);
                    $(".alertCheckBox").each(function(){
                        if (!$(this).prop('checked')){
                            $("#checkAll").prop('checked',false);
                        }
                    });
                }
                
            });
        }, 
        "bAutoWidth": false
    });
    tableMakeEditable(alertsTable);

    var loadAlertsOnSuccess=function (data, textStatus, jqXHR){
        if(data.status_code=='1'){
            alerts_arr=new Array(); 
            var alert_types=data.alert_type;
            if(data.alerts==null) 
                return;
            for(var i=0;i<data.alerts.length;i++){
                 var alert=new Array(); 
                 alert[0]=data.alerts[i].id;
                 alert[1]="<div class=\"tooltip-demo\"><input type=\"checkbox\" class=\"alertCheckBox\" id=\"alert"+data.alerts[i].id+"\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Alert id:"+data.alerts[i].id+"\"></input></div>";
                 //mouse over to show alert id
                 //add price, 52 w l h...for info purpose
                 alert[2]= "<div class=\"tooltip-demo\"><button type=\"button\" class=\"btn btn-default\" onClick=\"window.open('"+data.alerts[i].link+"', '_blank')\" data-toggle=\"tooltip\" data-placement=\"top\" title=\""+data.alerts[i].name+"\" style=\"width: 50px; height: 23px; padding:1px 5px;\">"+data.alerts[i].sid+"</button></div>";

                 alert[3]="current price & 52 w l h vis";

                var option1Str="<option value=\"3\">Specified Price &gt;</option>";
                var option2Str="<option value=\"4\">Specified Price &lt;</option>";
                var option3Str="<option value=\"5\">Daily % Increase &gt;</option>";
                var option4Str="<option value=\"6\">Daily % Decrease &gt;</option>";
                if(data.alerts[i].type=="3")
                    option1Str="<option value=\"3\" selected>Specified Price &gt;</option>";
                if(data.alerts[i].type=="4")
                    option2Str="<option value=\"4\" selected>Specified Price &lt;</option>";
                if(data.alerts[i].type=="5")
                    option3Str="<option value=\"5\" selected>Daily % Increase &gt;</option>";
                if(data.alerts[i].type=="6")
                    option4Str="<option value=\"6\" selected>Daily % Decrease &gt;</option>";
                alert[4]="<select id=\"type-"+data.alerts[i].id+"\"class=\"form-control\">"+option1Str+option2Str+option3Str+option4Str+"</select>";
                                    
                                  
                 if(data.alerts[i].type=="3"||data.alerts[i].type=="4"){
                    alert[5]=data.alerts[i].specified_price;
                 }else{
                    alert[5]=data.alerts[i].daily_percent+"%";
                 }

                 alert[6]=data.alerts[i].renotify_diff_percent+"%";
                 if(data.alerts[i].enable=="1"){

                    alert[7]="<div id=\"enable-"+data.alerts[i].id+"\" ><input type=\"checkbox\" name=\"enable-checkbox\" checked data-size=\"mini\" data-on-text=\"Y\" data-off-text=\"N\" data-on-color=\"primary\" data-off-color=\"warning\"></div>";
                 }else{
                    alert[7]="<div id=\"enable-"+data.alerts[i].id+"\" ><input type=\"checkbox\" name=\"enable-checkbox\" data-size=\"mini\" data-on-text=\"Y\" data-off-text=\"N\" data-on-color=\"primary\" data-off-color=\"warning\"></div>";
                 }
                 
                 var date_valid_till;
                 if(data.alerts[i].valid_till==null){
                    date_valid_till="";
                 }else{
                    date_valid_till=data.alerts[i].valid_till;
                 }
                 alert[8]="<div name=\"datetimepicker\" id=\"valid_till-"+data.alerts[i].id+"\" class=\"input-append date input-group\"><span class=\"input-group-btn add-on\" style=\"height:27px; width:38px;\"><button class=\"btn btn-default\" type=\"button\" style=\"height:27px; width:38px;\"><i class=\"fa fa-calendar\"></i></button></span><input name=\"datetime\" id=\"datetime-"+data.alerts[i].id+"\" type=\"text\" class=\"form-control\" data-format=\"yyyy-MM-dd\" placeholder=\"Valid Till\" style=\"height:27px; width:100px;\" value=\""+date_valid_till+"\"></div>";
               
                 //========hidden fields=========================//
                 //alert id , not displayed but use as row id for matching 
                 
                 alert[16]="old";//identify the alerts are old record, not newly added
             
                 //data for sort
                 alert[9]=data.alerts[i].enable;
                 alert[10]=date_valid_till;
                 
                 alerts_arr[i]=alert;

            }

            doTableRefresh(alertsTable,alerts_arr);
            doTableUISetUp();
        }else{
            checkRedirectNeeded_status_code(data.status_code);
        }
    };

    //get & load the alerts on page load, asyn=false to prevent the datatables is initialised before data is ready
    getAlerts(loadAlertsOnSuccess);

}

function doTableRefresh(table,data) {
    table.fnClearTable();
    table.fnAddData(data);
    tableMakeEditable(table);
    table.fnDraw();
}

function doTableUISetUp(){

    $('.tooltip-demo').tooltip({
        selector: "[data-toggle=tooltip]",
        container: "body"
    });

    $("input[name='enable-checkbox']").bootstrapSwitch();
    $('input[name="enable-checkbox"]').on('switchChange.bootstrapSwitch', function(event, state) {
        var formdata = new FormData();
        formdata.append( 'alert_id',event.target.parentNode.parentNode.parentNode.id.replace('enable-',''));
        formdata.append( 'field',"enable");

        if(state==true){
            formdata.append( 'value', 1);
        }else{
            formdata.append( 'value', 0);
        }

        updateAlert(formdata,null);

    });//end switch listener 

    $('div[name="datetimepicker"]').datetimepicker({
      language: 'pt-BR',
      pickTime: false
    });

    $('div[name="datetimepicker"]').on("changeDate",function (e) {
           var alert_id=e.target.id.replace("valid_till-","");
           var date=$('#datetime-'+alert_id).val();
           //console.log(alert_id+"  "+date);
           var formdata = new FormData();
           formdata.append( 'alert_id',alert_id);
           formdata.append( 'field',"valid_till");
           formdata.append( 'value',date);

           updateAlert(formdata,null);
    });

}

function tableMakeEditable(table){
    table.makeEditable({
        //...
        sUpdateURL: function(value, settings){
            var rowId = table.fnGetPosition(this)[0];
            var columnPosition = table.fnGetPosition(this)[1];
            var aData = table.fnGetData(rowId);
            console.log(rowId+"  "+columnPosition);

            var formdata = new FormData();
            formdata.append( 'alert_id',aData[0]);
            if(columnPosition==4){
                var alert_type=$( '#type-'+aData[0]+' option:selected').val();
                var formdata2 = new FormData();
                formdata2.append( 'alert_id',aData[0]);
                formdata2.append( 'field',"type");
                formdata2.append( 'value',alert_type);
                updateAlert(formdata2,null);//first reset the alert type first, do it in a seperate ajax call

                if(alert_type=="3"||alert_type=="4"){
                    formdata.append( 'field',"specified_price");
                }else if(alert_type=="5"||alert_type=="6"){
                    formdata.append( 'field',"daily_percent");
                }

            }else if (columnPosition==5){
                formdata.append( 'field',"renotify_diff_percent");
            }
            formdata.append( 'value',value);

            /**** on success handling is missing ****/
            updateAlert(formdata,null);

            return value
        },
        "aoColumns": [
            null,
            null,
            null,
            null,
            {}, 
            {},
            null,
            null
        ]
   
    }); 
}
/*
 * End of Alerts DataTable Related
 */


/*
 * Add Alerts Modal Diag Related
 */
 function addAlertInit(){
     $( "#stockcode_inputbox" ).change(function() {
         if($( "#stockcode_inputbox").val()==""){
            $("#alert_type").prop('disabled', true);
            return;
         }
         var onSuccess_cb=function(data, textStatus, jqXHR){
             if(data.status_code=='1'){

                $("#alert_type").prop('disabled', false);

                $("#stock_info_box_name").val(data.stock_info.name);
                stockname=data.stock_info.name;

              

                if(data.price.length>0){
                    $("#stock_info_box_price").val("Current Price: "+data.price[0].price+"   Lot Size: "+data.stock_info.lot_size);
                }else{
                    $("#stock_info_box_price").val("Current Price: 'to be fetching'   Lot Size: "+data.stock_info.lot_size);
                }
             }else{
                $("#stock_info_box_name").val(data.message);
                $("#alert_type").prop('disabled', true);
                $("#stock_info_box_price").val("");

                checkRedirectNeeded_status_code(data.status_code);
             }
        };

        getStock($( "#stockcode_inputbox").val(),onSuccess_cb);
    });

    $( "#addAlert" ).click(function(event) {
        event.preventDefault();

        var stock_id;
        var alert_type;
        var specified_price;
        var daily_percent;
        var renotify_diff_percent;
        var valid_till;//if not filled in...default not expire

       if($('#stock_info_box_name').val()==="No such stock!" ||$( '#stockcode_inputbox' ).val()==="" || $('#alert_trigger_condition').val()===""){
            //do nth since the form is incomplete
            return;
       }else{
            stock_id=$( '#stockcode_inputbox' ).val();
            alert_type=$( '#alert_type option:selected').val();

            if(alert_type=="3"||alert_type=="4"){
                specified_price=$( '#alert_trigger_condition' ).val();
                daily_percent=null;
            }else{
                specified_price=null;
                daily_percent=$( '#alert_trigger_condition' ).val();
            }

            if($( '#renotify_percent' ).val()==""){
                 renotify_percent=null;
            }else{
                 renotify_percent=$( '#renotify_percent' ).val();
            }
           
            if($( '#datetime' ).val()==""){
                 valid_till=null;
            }else{
                 valid_till=$( '#datetime' ).val();
            }
           

       }
        var formdata = new FormData();
        formdata.append( 'stock_id', stock_id);
        formdata.append( 'alert_type',alert_type);
        formdata.append( 'specified_price', specified_price);
        formdata.append( 'daily_percent', daily_percent);
        formdata.append( 'renotify_percent', renotify_percent);
        formdata.append( 'valid_till', valid_till);

        var onSuccess_cb=function(data, textStatus, jqXHR){
             if(data.status_code==1){
                 //reset the form after successfully done it.
                $("#resetForm").click();
                $("#msg_div").prop('class', 'form-group has-success');
                $("#msg_heading").text("Alert trigger added successfully, alert ID: "+data.alert_id);
             }else{
                $("#msg_div").prop('class', 'form-group has-error');
                $("#msg_heading").text(data.message);

                checkRedirectNeeded_status_code(data.status_code);
             }
        };

        addAlert(formdata,onSuccess_cb);
    });
    
    $( "#resetForm" ).click(function() {
         $("#msg_div").prop('class', 'form-group has-success');
         $("#msg_heading").text("Add a New Alert Trigger");
    });

 }
 /*
  * End of Add Alerts Modal Diag Related
  */


function deleteSelectedAlerts(){
     var selectedDeleteAlertIdArray=new Array();
     var newAlertsArray=new Array();
     for(var i=0;i<alerts_arr.length;i++){
        if($("#alert"+alerts_arr[i][0]).prop('checked')){
            selectedDeleteAlertIdArray.push(alerts_arr[i][0]);
        }else{
            newAlertsArray.push(alerts_arr[i]);
        }

     }
     //console.log(selectedDeleteAlertIdArray);

     var formdata = new FormData();
     formdata.append( 'alert_id_array',selectedDeleteAlertIdArray);

     var deleteAlertsOnSuccess=function (data, textStatus, jqXHR){
        if(data.status_code=='1'){
            if(data.delete_success==1){
                alerts_arr=newAlertsArray;
                doTableRefresh(alertsTable,alerts_arr);
                doTableUISetUp();
            }else{
                //do nth to update UI as delete failed.
                //instead, refresh the whole page
                location.reload();
            }
        }else{
            checkRedirectNeeded_status_code(data.status_code);
        }
    };

    deleteAlert(formdata,deleteAlertsOnSuccess);

}


