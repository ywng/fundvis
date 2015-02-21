//var ======================================================

/* user related */
var fblogin_URL='user/fblogin';
var login_URL='user/login';

/* stock */
var getStock_URL='stock/getStock';
var recordStockVisit_URL='stock/recordStockVisit';

/* transaction */
var addTransRecord_URL='transaction/addTransactionRecord';
var getSellableQuantity_URL='transaction/getSellableQuantity';
var getUserTransRecord_URL='transaction/getUserTransRecord';

/* alert */
var addAlert_URL='alert/addAlert';
var deleteAlert_URL='alert/deleteAlert';
var getAlerts_URL='alert/getAlerts';
var updateAlert_URL='alert/updateAlert';


//functions ==================================================


/* user related */
function logout(access_token){
	localStorage.clear();
	checkRedirectNeeded();
}

function login(email, password){
	var data=new FormData();
	data.append( 'email', email);
	data.append( 'password', password);

	rawAjaxCall(login_URL,"POST",true,data,loginOnSuccess,onFailure);
}

function fblogin(access_token){
	var data=new FormData();
	data.append( 'access_token', access_token);

	rawAjaxCall(fblogin_URL,"POST",true,data,loginOnSuccess,onFailure);
}

/* stock */

function getSellableQuantity(code,onSuccess){
	var data=new FormData();
	data.append( 'code', code);

	rawAjaxCall(getSellableQuantity_URL,"POST",true,data,onSuccess,onFailure);
}

function getStock(code,onSuccess){
	var data=new FormData();
	data.append( 'code', code);

	rawAjaxCall(getStock_URL,"POST",true,data,onSuccess,onFailure);
}

function recordStockVisit(code){
	var data=new FormData();
	data.append( 'code', code);

	rawAjaxCall(recordStockVisit_URL,"POST",true,data,null,onFailure);
}



/* transaction */
function addTransRecord(form_data,onSuccess){
	rawAjaxCall(addTransRecord_URL,"POST",true,form_data,onSuccess,onFailure);
}
function getUserTransRecord(onSuccess){
	rawAjaxCall(getUserTransRecord_URL,"GET",true,null,onSuccess,onFailure);
}




/* alert */
function addAlert(form_data,onSuccess){
	rawAjaxCall(addAlert_URL,"POST",true,form_data,onSuccess,onFailure);
}

function deleteAlert(form_data,onSuccess){
	rawAjaxCall(deleteAlert_URL,"POST",true,form_data,onSuccess,onFailure);
}

function getAlerts(onSuccess){
	rawAjaxCall(getAlerts_URL,"GET",true,null,onSuccess,onFailure);
}

function updateAlert(form_data,onSuccess){
	rawAjaxCall(updateAlert_URL,"POST",true,form_data,onSuccess,onFailure);
}


// helpers ======================================================
/** generic ajax call */
/** other calls are just build onto this */
function rawAjaxCall(relativeURL,type,async_setting,data,onSuccess,onFailure){

	$.ajax({
        url: apiDomain + relativeURL,
        contentType:false,
		processData: false,
		cache: false,
		async:async_setting,
        data:data,
        headers : {"X-session-token":localStorage.getItem("X-WealthVis-session-token"),"X-email":localStorage.getItem("X-WealthVis-email"),"X-user-type":localStorage.getItem("X-WealthVis-user-type")},
        type: type, 
        success: onSuccess,
        error: onFailure

    }); // end of the ajax call

}


/* common onFailure / onSuccess callback **/
var onFailure=function(jqHXR, textStatus, errorThrown){
	console.log('ajax error:' +textStatus + ' ' + errorThrown);
};

function loginOnSuccess(data, textStatus, jqXHR){
		 if(data.status_code=='1'){
		 	localStorage.setItem("X-WealthVis-session-token", data.session_token);
            localStorage.setItem("X-WealthVis-email", data.email);
            localStorage.setItem("X-WealthVis-user-type",'user');
            localStorage.setItem("X-WealthVis-loggedIn", "true");
            localStorage.setItem("X-WealthVis-expire-time", data.expire_time);

            if(data.user_fbid!=null)
           	 localStorage.setItem("X-WealthVis-fbid", data.user_fbid);

            checkLogin();
		 }
}

 