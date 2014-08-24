
function checkLogin(){
	if(localStorage.getItem("X-WealthVis-loggedIn")=="true"){
	   
	    if(localStorage.getItem("X-WealthVis-user-type")=="user"){
	        $(location).attr('href', serverDomain);
	    } 

	}
}

function checkRedirectNeeded(){
	if(!localStorage.getItem("X-WealthVis-loggedIn")||!localStorage.getItem("X-WealthVis-session-token")||localStorage.getItem("X-WealthVis-loggedIn")=="false"){//no session records, redirect to login
		$(location).attr('href', serverDomain + 'login.html');
	}
}

function checkRedirectNeeded_status_code(status_code){
	if(status_code!=null && status_code=="-1"){//credential check failed, redirect to login
		localStorage.setItem("X-WealthVis-loggedIn", "false");
		$(location).attr('href', serverDomain + 'login.html');
	}
}

function convertDate(inputFormat) {
  //console.log(inputFormat);
  function pad(s) { return (s < 10) ? '0' + s : s; }
  var d = new Date(inputFormat);
  return [pad(d.getDate()), pad(d.getMonth()+1), d.getFullYear()].join('-');
}