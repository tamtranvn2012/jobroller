	window.fbAsyncInit = function() {
		var dir = location.pathname.substring(0,location.pathname.lastIndexOf('/')+1); 	
		var FB_appId = document.getElementById('FB_appId').value; 

		FB.init({ 					   
			appId      : FB_appId,				   
			channelUrl : window.location.protocol+'//' + dir + '/channel.html', 
			status     : true,  
			cookie     : true, 
			xfbml      : true 
		}); 				   
	}; 				    
	function onLogin(){
	FB.getLoginStatus(function(response) {
		if (response.status === 'connected') {
			getFacebookData();
		} 
		else if (response.status === 'not_authorized') {
				login(); 					   
			}  else {
				login(); 					   
			} 					  
	}); 				   
	} 				   
	function login() { 					 
		FB.login(function(response) { 						 
			if (response.authResponse) { 							 
				getFacebookData(); 						 
			} else { 				 
			} 					 
		}); 				 
	} 				 
	function getFacebookData() 
	{   
		FB.api('/me', function(response) {  
			var  flashObj = document.getElementById('index_embed'); 	
			flashObj.afterLogin(response); 					 
		}); 				 
	} 				    
	(function(d){ 		 
		var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0]; 	 
		if (d.getElementById(id)) {return;} 					  
		js = d.createElement('script'); js.id = id; js.async = true; 					  
		js.src = '//connect.facebook.net/en_US/all.js'; 	 
		ref.parentNode.insertBefore(js, ref); 				    
	}(document));