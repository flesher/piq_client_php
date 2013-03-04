window.fbAsyncInit = function() {
// init the FB JS SDK
FB.init({
  appId      : '344766768961775', // App ID from the App Dashboard
  channelUrl : '//piq.fm/ui_beta/channel.html', // Channel File for x-domain communication
  status     : true, // check the login status upon init?
  cookie     : true, // set sessions cookies to allow your server to access the session?
  xfbml      : true  // parse XFBML tags on this page?
});

FB.getLoginStatus(function(response) {
	
  if (response.status === 'connected') {
    $('#login').html("logout");
  	$('#login').click(function(){
			FB.logout(function(response){
				console.log(response);
			});
				;
		});
  } else if (response.status === 'not_authorized') {
    $('#login').html("login with facebook");
  	$('#login').click(function(){
    	FB.login(function(response){
    		console.log(response);
    	});
    });
  } else {
  	$('#login').html("login with facebook");
  	$('#login').click(function(){
    	FB.login(function(response){
    		$('#login').html("logout");
    		$('#login').click(function(){
    			FB.logout();
    		}
    	});
    });
  }

});

// Additional initialization code such as adding Event Listeners goes here

};

// Load the SDK's source Asynchronously
// Note that the debug version is being actively developed and might 
// contain some type checks that are overly strict. 
// Please report such bugs using the bugs tool.
(function(d, debug){
var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
if (d.getElementById(id)) {return;}
js = d.createElement('script'); js.id = id; js.async = true;
js.src = "//connect.facebook.net/en_US/all" + (debug ? "/debug" : "") + ".js";
ref.parentNode.insertBefore(js, ref);
}(document, /*debug*/ false));
