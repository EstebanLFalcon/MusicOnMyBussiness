<?php
		$session = new SpotifyWebAPI\Session('78fe14946efe45f285d840b72bea40e4', '54db8e2ab9204bc08d09da67577c3bee', 'http://localhost:8000/');
		$api = new SpotifyWebAPI\SpotifyWebAPI();
		$session->getAuthorizeUrl(array('scope' => array('user-read-email', 'user-library-modify','user-read-private')),true);
		//echo "<a href='" . $session->getAuthorizeUrl(array('scope' => array('user-read-email', 'user-library-modify','user-read-private')),true) . "'>Login</a>";
		if (isset($_GET['code'])) {
	    $session->requestToken($_GET['code']);
	   	$api->setAccessToken($session->getAccessToken());
	    print_r($api->me());
	    		}
	?>