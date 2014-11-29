
<html lang="en">
<head>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script>

	$(document).ready(function() {
		$.ajaxSetup({ cache: true });
		$.getScript('//connect.facebook.net/en_UK/all.js', function(){
			FB.init({
				appId: '394938824006318',
			});     
			FB.getLoginStatus(function(response) {
				statusChangeCallback(response);
			});
		});
		$('#logout').hide();
		$('#start-broadcasting').hide();

		$('#start-broadcasting').click(function(){
			broadcast();
		});
		
		$('#login').click(function(){
			FB.login(function(response){
				if (response.authResponse) 
				{
					$('#login').hide();
					$('#logout').show();
					testAPI();
					getPages();
					$('#start-broadcasting').show();
					spotifySessionCall();
				} 
				else
				{
					console.log('Authorization failed.');
				}
			},{scope: 'manage_pages, publish_actions'});
		});
		
		$('#logout').click(function(){
			FB.logout(function(){
				document.location.reload();
			});
		});
	});
	
	function statusChangeCallback(response) {
		console.log('statusChangeCallback');
		console.log(response);
		if (response.status === 'connected') {
			$('#login').hide();
			testAPI();
			getPages();
			$('#logout').show();
			$('#start-broadcasting').show();
			spotifySessionCall();
		} 
		/*
		else if (response.status === 'not_authorized') {
		  document.getElementById('status').innerHTML = 'Please log ' +
			'into this app.';
		} else {
		  document.getElementById('status').innerHTML = 'Please log ' +
			'into Facebook.';
		}
		*/
	}

	function testAPI() {
		console.log('Welcome!  Fetching your information.... ');
		FB.api('/me', function(response) {
		  console.log('Successful login for: ' + response.name);
		  document.getElementById('status').innerHTML = 'Thanks for logging in, ' + response.name + '!';
			
		});
	}
	
	function getPages() {
		FB.api('/me/accounts', function(response) {
			var l = response.data.length;
			var pages = "Pages: <br>";
		
			for(var i = 0; i < l; i++)
			{
				pages += "<input type='radio' name='page' value='" + response.data[i].id + "'>";
				pages += response.data[i].name + "<br>";// + "<br>ID: " + response.data[i].id + "<br>";
			}
			$('#user').html(pages);
 
        });
	}
	
	function broadcast(){
	
		var selectedPage = "";
		var selected = $("input[type='radio'][name='page']:checked");
		if (selected.length > 0) {
			selectedPage = selected.val();
		}
		else
		{
			alert('choose a page first');
			return;
		}
		
		setInterval(function () {
			var newURL = selectedPage + '/tagged';
			console.log("new url " + newURL);
			
			FB.api(newURL, function(responsePage){
				var postTotales = responsePage.data.length;
				for (var j = 0; j < postTotales; j++)
				{
					var postMessage = responsePage.data[j].message;
					if(postMessage.charAt(0) == '%')
					{
						var post1 = responsePage.data[j].id;
						// add song to playlist (post message)
						FB.api(post1, "DELETE", function(responseDeletePost){
							if(responseDeletePost.success)
							{
								console.log("post borrado: " + postMessage);
							}
						});
					}
					else
					{
						console.log("post no borrado: "+postMessage);
					}
					//pages += "id: " + responsePage.data[j].id + "<br>From: " + responsePage.data[j].from.name + "<br>Message: " + responsePage.data[j].message;
				}
				//$('#user').html(pages);
			});
		}, 60000);
	}

	function spotifySessionCall()
	{
		$.ajax({
			type: "POST",
			url: "/spotifyLogin",
			dataType: "json", 
			success: function(response){
				window.location.href = response.authorizeUrl;
			},
			failure: function (response) {
				alert(response.d);
			}
		});
	}
	
</script>

	<meta charset="UTF-8">
	<title>Music On My Business</title>
</head>
<body>

<button id="login">Inicia sesion con Facebook
</button>

<button id="logout">Cerrar sesion
</button>

<div id="status">
</div>

<div id="user">
</div>

<div id="start-broadcasting">
	<button id="start-button">
		Start Broadcasting
	</button>
</div>
 	<?php
	
			if (isset($_GET['code'])) {
				Session::put('authCode',$_GET['code']);
				$session = Session::get("spotifySession");
				$api = Session::get("api");
			    $session->requestToken(Session::get("authCode"));
			   	$api->setAccessToken($session->getAccessToken());
			   	echo $api->me().display_name;
			    print_r($api->me());

		    }
			
	?> 
</body>
</html>
