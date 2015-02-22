var stockname="";

$(document).ready(function() {
    //load common side bar & init the side bar by callback function
    $('#nav_bar').load("nav.html",sideBarInit);
    checkRedirectNeeded();

    addTransactionsOperationInit();
    transDataTableInit();

    checkRedirectNeeded();
});

/*
 * Transactions DataTable Related
 */
var transactionTable;
var transactions_arr=null;

function transDataTableInit(){

    transactionTable=$('#dataTables-trans').dataTable({ 
        "iDisplayLength": 5,
        "aaData":transactions_arr,
        "aoColumns": [
            {"bSortable": false, "bVisible": false},//id 
            { "bSortable": false, "bSearchable":false, "sWidth": "5px" },//select
            { "bSortable": true, "bSearchable": true, "sWidth": "50px" },//stock rectangle
            {},
            {},
            {},
            {},
            {},
            {},
            {},
            {},
            {},
            {"bSortable": false, "bSearchable": true, "sWidth": "450px" },
            {"bSortable": false, "bSearchable": true, "sWidth": "450px" },
            {},
            {}

        ],
        "fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
            
        },
        "bAutoWidth": false
    });
    tableMakeEditable(transactionTable);

    var loadUserTransOnSuccess=function (data, textStatus, jqXHR){
        if(data.status_code=='1'){
            transactions_arr=new Array(); 
            for(var i=0;i<data.user_trans_record.length;i++){
                 var transaction=new Array(); 
                 transaction[0]=data.user_trans_record[i].id;
                 transaction[1]="<div class=\"tooltip-demo\"><input type=\"checkbox\" id=\"alert"+data.user_trans_record[i].id+"\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Transaction id:"+data.user_trans_record[i].id+"\"></input></div>";
                 transaction[2]="<div class=\"tooltip-demo\"><button type=\"button\" class=\"btn btn-default\" data-toggle=\"tooltip\" data-placement=\"top\" title=\""+data.user_trans_record[i].name+"\" onClick=\"window.open('"+data.user_trans_record[i].link+"', '_blank')\" style=\"width: 50px; height: 23px; padding:1px 5px;\">"+data.user_trans_record[i].sid+"</button></div>";
                 transaction[3]=data.user_trans_record[i].type;
                 transaction[4]=data.user_trans_record[i].price;
                 transaction[5]=data.user_trans_record[i].target_price;
                 transaction[6]=data.user_trans_record[i].target_price_renotify_percent;
                 transaction[7]=data.user_trans_record[i].stop_loss_price;
                 transaction[8]=data.user_trans_record[i].stop_loss_price_renotify_percent;
                 transaction[9]=data.user_trans_record[i].quantity;
                 transaction[10]=data.user_trans_record[i].trans_fee;
                 transaction[11]=data.user_trans_record[i].datetime.split(" ")[0];
                 transaction[12]=data.user_trans_record[i].rationale;
                 transaction[13]=data.user_trans_record[i].review;
                 transaction[14]=data.user_trans_record[i].closed_quantity;
                 transaction[15]=data.user_trans_record[i].closed_price;

                 //========hidden fields=========================//
                 //alert id , not displayed but use as row id for matching 
                 
                 transaction[20]="old";//identify the alerts are old record, not newly added
             
                 //data for sort
            
                 
                 transactions_arr[i]=transaction;

            }

            doTableRefresh(transactionTable,transactions_arr);
            doTableUISetUp();
        }else{
            checkRedirectNeeded_status_code(data.status_code);
        }
    };

    getUserTransRecord(loadUserTransOnSuccess);


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
           /*formdata.append( 'alert_id',aData[0]);
            if(columnPosition==4){
                var alert_type=$( '#type-'+aData[0]+' option:selected').val();
                var formdata2 = new FormData();
                formdata2.append( 'alert_id',aData[0]);
                formdata2.append( 'field',"type");
                formdata2.append( 'value',alert_type);
                updateAlert(formdata2,null);//first reset the alert type first

                if(alert_type=="3"||alert_type=="4"){
                    formdata.append( 'field',"specified_price");
                }else if(alert_type=="5"||alert_type=="6"){
                    formdata.append( 'field',"daily_percent");
                }

            }else if (columnPosition==5){
                formdata.append( 'field',"renotify_diff_percent");
            }
            formdata.append( 'value',value);

            updateAlert(formdata,null);*/

            return value;
        },
        "aoColumns": [
            null,
            null,
            null,
            null,
            {},
            {},
            {},
            {},
            {},
            {},
            {},
            null,
            {},
            {},
            null,
            null
        ]
   
    }); 
}
/*
 * End of Transactions DataTable Related
 */



/*
 * Add Transactions Modal Diag Related
 */
function addTransactionsOperationInit(){
    $('#datetimepicker1').datetimepicker({
      language: 'pt-BR'
    });

    $( "#trans_type" ).change(function() {
        if($( '#trans_type option:selected' ).text()=="Sell"||$( '#trans_type option:selected' ).text()=="Type"){
            $("#target_price").prop('disabled', true);
            $("#stop_loss_price").prop('disabled', true);
            $("#target_price_renotify_percent").prop('disabled', true);
            $("#stop_loss_price_renotify_percent").prop('disabled', true);
           
            $("#target_price").val("");
            $("#stop_loss_price").val("");
            $("#target_price_renotify_percent").val("");
            $("#stop_loss_price_renotify_percent").val("");
        }else{
            $("#target_price").prop('disabled', false);
            $("#stop_loss_price").prop('disabled', false);
            $("#target_price_renotify_percent").prop('disabled', false);
            $("#stop_loss_price_renotify_percent").prop('disabled', false);
        }

        showSellableQuantity();

    });

    $( "#stockcode_inputbox" ).change(function() {
         var onSuccess_cb=function(data, textStatus, jqXHR){
             if(data.status_code=='1'){

                $("#trans_type").prop('disabled', false);

                $("#stock_info_box_name").val(data.stock_info.name);
                stockname=data.stock_info.name;

                showSellableQuantity();

                if(data.price.length>0){
                    $("#stock_info_box_price").val("Current Price: "+data.price[0].price+"   Lot Size: "+data.stock_info.lot_size);
                }else{
                    $("#stock_info_box_price").val("Current Price: 'to be fetching'   Lot Size: "+data.stock_info.lot_size);
                }
             }else{
                $("#stock_info_box_name").val(data.message);
                $("#stock_info_box_price").val("");

                checkRedirectNeeded_status_code(data.status_code);
             }
        };

        getStock($( "#stockcode_inputbox").val(),onSuccess_cb);
    });

    $( "#addTransRecord" ).click(function(event) {
        event.preventDefault();

        if($('#stock_info_box_name').val()==="No such stock!" ||$( '#stockcode_inputbox' ).val()==="" || $('#trans_type option:selected' ).text()==="Type" || $('#price').val()==="" || $('#quantity').val()==="")
            return;
        var formdata = new FormData();
        formdata.append( 'stock_id', $( '#stockcode_inputbox' ).val());
        formdata.append( 'type', $( '#trans_type option:selected' ).text());
        formdata.append( 'price', $( '#price' ).val());
        formdata.append( 'target_price', $( '#target_price' ).val());
        formdata.append( 'stop_loss_price', $( '#stop_loss_price' ).val());
        formdata.append( 'target_price_renotify_percent', $( '#target_price_renotify_percent' ).val());
        formdata.append( 'stop_loss_price_renotify_percent', $( '#stop_loss_price_renotify_percent' ).val());
        formdata.append( 'rationale', $( '#rationale' ).val());
        formdata.append( 'review', $( '#review' ).val());
        formdata.append( 'trans_fee', $( '#trans_fee' ).val());
        formdata.append( 'quantity', $( '#quantity' ).val());
        formdata.append( 'datetime', $( '#datetime' ).val());


        var onSuccess_cb=function(data, textStatus, jqXHR){
             if(data.status_code==1){
                 //reset the form after successfully done it.
                $("#resetForm").click();
                $("#msg_div").prop('class', 'form-group has-success');
                $("#msg_heading").text("Transaction added successfully, transaction ID: "+data.transaction_id);
             }else{
                $("#msg_div").prop('class', 'form-group has-error');
                $("#msg_heading").text(data.message);

                checkRedirectNeeded_status_code(data.status_code);
             }
        };

        addTransRecord(formdata,onSuccess_cb);
    });
    
    $( "#resetForm" ).click(function() {
         $("#msg_div").prop('class', 'form-group has-success');
         $("#msg_heading").text("Add a New Transaction");
    });

}

function showSellableQuantity(){
    if($( '#trans_type option:selected' ).text()=="Sell" || $( '#trans_type option:selected' ).text()=="Hold"){

        var onSuccess_cb=function(data, textStatus, jqXHR){
             if(data.status_code=='1'){
                if(data.sellable_quantity>0)
                     $('#stock_info_box_name').val(stockname+"  Sellable Quan: "+data.sellable_quantity);
             }else{
                checkRedirectNeeded_status_code(data.status_code);
                
             }
        };
        getSellableQuantity($( "#stockcode_inputbox").val(),onSuccess_cb);
       
    }
}

/*
 * End of Add Transactions Modal Diag Related
 */

