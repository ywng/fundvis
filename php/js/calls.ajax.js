//var 
var fblogin_URL='user/fblogin';
var login_URL='user/login';
var getStock_URL='stock/getStock';
var addTransRecord_URL='transaction/addTransactionRecord';
var addAlert_URL='alert/addAlert'
var getSellableQuantity_URL='transaction/getSellableQuantity';

var onFailure=function(jqHXR, textStatus, errorThrown){
		 console.log('ajax error:' +textStatus + ' ' + errorThrown);
	};

function logout(access_token){
	localStorage.clear();
	checkRedirectNeeded();
}

function getSellableQuantity(code,onSuccess){
	
	var data=new FormData();
	data.append( 'code', code);

	rawAjaxCall(getSellableQuantity_URL,"POST",data,onSuccess,onFailure);
}

function getStock(code,onSuccess){
	
	var data=new FormData();
	data.append( 'code', code);

	rawAjaxCall(getStock_URL,"POST",data,onSuccess,onFailure);
}

function addTransRecord(form_data,onSuccess){
	
	rawAjaxCall(addTransRecord_URL,"POST",form_data,onSuccess,onFailure);
}

function addAlert(form_data,onSuccess){
	
	rawAjaxCall(addAlert_URL,"POST",form_data,onSuccess,onFailure);
}

function login(email, password){

	var data=new FormData();
	data.append( 'email', email);
	data.append( 'password', password);

	rawAjaxCall(login_URL,"POST",data,loginOnSuccess,onFailure);
}

function fblogin(access_token){

	var data=new FormData();
	data.append( 'access_token', access_token);

	rawAjaxCall(fblogin_URL,"POST",data,loginOnSuccess,onFailure);
}


function rawAjaxCall(relativeURL,type,data,onSuccess,onFailure){

	$.ajax({
        url: apiDomain + relativeURL,
        contentType:false,
		processData: false,
		cache: false,
        data:data,
        headers : {"X-session-token":localStorage.getItem("X-WealthVis-session-token"),"X-email":localStorage.getItem("X-WealthVis-email"),"X-user-type":localStorage.getItem("X-WealthVis-user-type")},
        type: type, 
        success: onSuccess,
        error: onFailure

    }); // end of the ajax call

}

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

 