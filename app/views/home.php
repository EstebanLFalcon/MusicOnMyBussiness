
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Music on My Business</title>

    <!-- Bootstrap core CSS -->
    <!--<link href="dist/css/bootstrap.min.css" rel="stylesheet">-->
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">

<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap-theme.min.css">

<!-- Latest compiled and minified JavaScript -->

    <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
    <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!-- Custom styles for this template -->
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script>
	var interval;
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
					$('#botonfb').hide();
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
			$('#botonfb').hide();
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

  </head>
<!-- NAVBAR
================================================== -->
  <body>

<!-- NAVBAR
    <div class="navbar-wrapper">
      <div class="container">

        <nav class="navbar navbar-inverse navbar-static-top" role="navigation">
          <div class="container">
            <div class="navbar-header">
              <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
              </button>
              <div class="navbar-brand" >Music on my Business</div>
            </div>
            <div id="navbar" class="navbar-collapse collapse">
              <ul class="nav navbar-nav">
                <li class="active"><a href="#">Home</a></li>
                <li><a href="#about">About</a></li>
                <li><a href="#contact">Contact</a></li>
                <li class="dropdown">
                  <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Dropdown <span class="caret"></span></a>
                  <ul class="dropdown-menu" role="menu">
                    <li><a href="#">Action</a></li>
                    <li><a href="#">Another action</a></li>
                    <li><a href="#">Something else here</a></li>
                    <li class="divider"></li>
                    <li class="dropdown-header">Nav header</li>
                    <li><a href="#">Separated link</a></li>
                    <li><a href="#">One more separated link</a></li>
                  </ul>
                </li>
              </ul>
            </div>
          </div>
        </nav>

      </div>
    </div>
  -->


    <!-- Carousel
    ================================================== -->
    <div id="myCarousel" class="carousel slide" data-ride="carousel">
      <!-- Indicators -->
      <ol class="carousel-indicators">
        <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
      </ol>
      <div class="carousel-inner" role="listbox">
        <div class="item active">
          <div class="banner" align="center">
            <img src="http://www.uri.edu/artsci/mus/resource/img/banner.jpg" alt="First slide" width="100%" align="center">
          </div>          
          <div class="container">
            <div class="carousel-caption">
              <h1>Play your favorite music.</h1>
              <p>Play your favorite music has never been easier.</p>
            </div>
          </div>
        </div>
      </div>
    </div><!-- /.carousel -->


    <!-- Marketing messaging and featurettes
    ================================================== -->
    <!-- Wrap the rest of the page in another container to center all the content. -->

    <div  class="container marketing">

      <!-- START THE FEATURETTES -->

      <hr class="featurette-divider">

      <div id="botonfb" class="row featurette">
        <div class="col-md-7">
          <h2 class="featurette-heading">What's up with this app?. <span class="text-muted">You post it, we play it!.</span></h2>
          <p class="lead">Music on my Business it's a simple web page which helps you to set some music as it were a JukeBox, but 
            much easier. To set your music up, you only have to connect to facebook and make a post to the business page, automatically
            the application will recognize the request, and will send it to a playlist on Spotify.</p>
        </div>
        <div class="col-md-5">
          
            <img id="login" class="featurette-image img-responsive" src="http://www.in2sports.co.za/image/fb-button.png" alt="Connect to FB">
          
        </div>
      </div>

      <hr class="featurette-divider">



      <hr class="featurette-divider">

      <!-- /END THE FEATURETTES -->


      <!-- FOOTER -->
      

<div id="status">
</div>

<div id="user">
</div>

<div id="start-broadcasting">
	<button id="start-button" class="btn btn-info">
		Start Broadcasting
	</button>

</div>
<div id="stop-broadcasting">
	<button id="stop-button" class="btn btn-success">
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

			   	
			   	foreach ($playlists->items as $playlist) {
				    echo "<input type='radio' name='playlist' value='" . $playlist->id . "' >" . $playlist->name;
				    echo '<br>';
				}

		    }
			
	?> 
    <button id="logout" class="btn btn-success">Cerrar sesion
</button>
      <footer>
        <p class="pull-right"><a href="#">Back to top</a></p>
        <p>&copy; 2014 Real Zamesta Programming. &middot;
      </footer>

    </div><!-- /.container -->


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
 
  </body>
</html>
