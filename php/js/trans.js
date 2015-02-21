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
        "iDisplayLength": 50,
        "aaData":transactions_arr,
       // "aoColumns": [
            
       //  ],
        "fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
            
        },
        "bAutoWidth": false
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

