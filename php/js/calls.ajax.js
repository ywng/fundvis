//var 
var fblogin_URL='user/fblogin'

function logout(access_token){
	if(localStorage.getItem("X-WealthVis-fbid")){
		FB.api('DELETE/'+localStorage.getItem("X-WealthVis-fbid")+'/permissions',function(response) {
	      console.log(response);
	      
	    });
	    localStorage.clear();
	}
	
}

function fblogin(access_token){

	var onSuccess=function(data, textStatus, jqXHR){
		 if(data.status_code=='1'){
		 	localStorage.setItem("X-WealthVis-session-token", data.session_token);
            localStorage.setItem("X-WealthVis-email", data.email);
            localStorage.setItem("X-WealthVis-user-type",'user');
            localStorage.setItem("X-WealthVis-loggedIn", "true");
            localStorage.setItem("X-WealthVis-expire-time", data.expire_time);

            localStorage.setItem("X-WealthVis-fbid", data.user_fbid);

            checkLogin();
		 }
	};

	var onFailure=function(jqHXR, textStatus, errorThrown){
		 console.log('ajax error:' +textStatus + ' ' + errorThrown);
	};

	var data="access_token="+access_token;
	rawAjaxCall(fblogin_URL,"POST",data,onSuccess,onFailure);
}


function rawAjaxCall(relativeURL,type,data,onSuccess,onFailure){

	$.ajax({
        url: apiDomain + relativeURL,
        data:data,
        headers : {"X-WealthVis-session-token":localStorage.getItem("X-WealthVis-session-token"),"X-WealthVis-email":localStorage.getItem("X-WealthVis-email"),"X-WealthVis-user-type":localStorage.getItem("X-WealthVis-user-type")},
        type: type, 
        success: onSuccess,
        error: onFailure

    }); // end of the ajax call

}


 