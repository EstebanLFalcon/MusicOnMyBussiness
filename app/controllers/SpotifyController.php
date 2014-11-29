<?php

class SpotifyController extends BaseController {

	public function login()
	{
		$session = new SpotifyWebAPI\Session('78fe14946efe45f285d840b72bea40e4', '54db8e2ab9204bc08d09da67577c3bee', 'http://localhost:8000/');
		$api = new SpotifyWebAPI\SpotifyWebAPI();
		$code = $session->getAuthorizeUrl(array('scope' => array('user-read-email', 'user-library-modify','user-read-private')),true);
		$s = [];
		$s['authorizeUrl'] = $code;
		Session::put('spotifySession', $session);
		Session::put('api', $api);
		echo json_encode($s);
	}


}
