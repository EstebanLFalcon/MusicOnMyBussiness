<html lang="en">
<head>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script>
	var interval;
	$(document).ready(function() {
		$.ajaxSetup({ cache: true });
		$.getScript('//connect.facebook.net/en_UK/all.js', function(){
			FB.init({
				appId: 'YOUR_AP_ID',
			});     
			FB.getLoginStatus(function(response) {
				statusChangeCallback(response);
			});
		});
		$('#logout').hide();
		$('#start-broadcasting').hide();
		$('#stop-broadcasting').hide();
		$('#start-broadcasting').click(function(){
			broadcast();
		});
		
		$('#stop-broadcasting').click(function(){
			$('#stop-broadcasting').hide();
			$('#start-broadcasting').show();
			stop();
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
	
	function stop(){
		window.clearInterval(interval);
	}

	function statusChangeCallback(response) {
		console.log('statusChangeCallback');
		console.log(response);
		if (response.status === 'connected') {
			$('#login').hide();
			testAPI();
			getPages();
			$('#logout').show();
			$('#start-broadcasting').show();
			//spotifySessionCall();
		} 
		
		else if (response.status === 'not_authorized') {
		  document.getElementById('status').innerHTML = 'Please log ' +
			'into this app.';
		} else {
		  document.getElementById('status').innerHTML = 'Please log ' +
			'into Facebook.';
		}
		
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
		var selectedPlaylist;
		var selected2 = $("input[type='radio'][name='playlist']:checked");
		if (selected2.length > 0) {
			selectedPlaylist = selected2.val();
		}
		else
		{
			alert('choose a playlist first');
			return;
		}
		$('#stop-broadcasting').show();
		$('#start-broadcasting').hide();

		interval=setInterval(function () {
			var newURL = selectedPage + '/tagged';
			console.log("new url " + newURL);
			
			FB.api(newURL, function(responsePage){
				var postTotales = responsePage.data.length;
				var jsonSongs=[];
				var jsonToSpotify = new Array();
				console.log('paso1');
				//jsonToSpotify="{'playlist_id':'"+selectedPlaylist+"',";
				//jsonToSpotify="[";
				for (var j = 0; j < postTotales; j++)
				{
					var postMessage = responsePage.data[j].message;
					if(postMessage.charAt(0) == '%')
					{
						var song = new Array();
						//console.log('paso1.5');

						var post1 = responsePage.data[j].id;
						var postSongArtist = postMessage.substring(1);
						var arraySongArtist = postSongArtist.split("-");
						for (var index=0; index<arraySongArtist.length; index++)
							arraySongArtist[index]=arraySongArtist[index].trim();
						//console.log('paso1.6');

						if(arraySongArtist.length==1)
						{
							song[0] = arraySongArtist[0];
							song[1] = "";
							song[2] = selectedPlaylist;
						}
							//jsonToSpotify+="{'song':'"+arraySongArtist[0]+"','artist':''},";
						else
						{
							song[0] = arraySongArtist[0];
							song[1] = arraySongArtist[1];
							song[2] = selectedPlaylist;
						}
						//console.log('paso2');
						spotifySearchTracks(selectedPlaylist, song[0], song[1]);
						//jsonToSpotify.push(song);
						//console.log('paso3');
							//jsonToSpotify+="{'song':'"+arraySongArtist[0]+"','artist':'"+arraySongArtist[1]+"'},";
						//console.log("{'song':'"+arraySongArtist[0]+"','artist':'"+arraySongArtist[1]+"'},");
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
				//jsonToSpotify=jsonToSpotify.substring(0,jsonToSpotify.length-1);
				//jsonToSpotify+="]";
				console.log(jsonToSpotify);
				//alert(jsonToSpotify);
				//spotifySearchTracks(selectedPlaylist,jsonToSpotify);
				//$('#user').html(pages);
			});
		}, 5000);
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
	
	function spotifySearchTracks(playlist_id, song, artist)
	{
		//alert(playlist_id);
		//alert(search_query);
		$.ajax({
			type: "POST",
			url: "/spotifySearchTrack",
			dataType: "json",
			data : {
				playlist_id: playlist_id,
				song : song,
				artist : artist
			},
			success: function(response){
				//window.location.href = response.authorizeUrl;
				console.log(response.songs_added);
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
<div id="stop-broadcasting">
	<button id="stop-button">
		Stop Broadcasting
	</button>
</div>
 	<?php
	
			if (isset($_GET['code'])) {
				Session::put('authCode',$_GET['code']);
				$session = Session::get("spotifySession");
				$api = Session::get("api");
			    $session->requestToken(Session::get("authCode"));
			   	$api->setAccessToken($session->getAccessToken());
			   	$user_data = $api->me();
			   	$user_id = $user_data->id;
			   	$playlists = $api->getUserPlaylists($user_id);
			   	
			   	Session::put('user_id',$user_id);

			   	echo $playlists->items[0]->id;
			   	foreach ($playlists->items as $playlist) {
				    echo "<input type='radio' name='playlist' value='" . $playlist->id . "' >" . $playlist->name;
				    echo '<br>';
				}

		    }
			
	?> 
</body>
</html>
