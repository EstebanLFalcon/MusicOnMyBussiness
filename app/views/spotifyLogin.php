<?php
		$session = new SpotifyWebAPI\Session('API_KEY', 'SECRET', 'http://localhost:8000/');
		$api = new SpotifyWebAPI\SpotifyWebAPI();
		$session->getAuthorizeUrl(array('scope' => array('user-read-email', 'user-library-modify','user-read-private')),true);
		//echo "<a href='" . $session->getAuthorizeUrl(array('scope' => array('user-read-email', 'user-library-modify','user-read-private')),true) . "'>Login</a>";
		if (isset($_GET['code'])) {
	    $session->requestToken($_GET['code']);
	   	$api->setAccessToken($session->getAccessToken());
	    print_r($api->me());
	    		}
	?>
