<?php

class SpotifyController extends BaseController {

	public function login()
	{
		$session = new SpotifyWebAPI\Session('78fe14946efe45f285d840b72bea40e4', '54db8e2ab9204bc08d09da67577c3bee', 'http://localhost:8000/');
		$api = new SpotifyWebAPI\SpotifyWebAPI();
		$code = $session->getAuthorizeUrl(array('scope' => array('user-read-email', 'user-library-modify','user-read-private')),true);
		$return_value = [];
		$return_value['authorizeUrl'] = $code;
		Session::put('spotifySession', $session);
		Session::put('api', $api);
		echo json_encode($return_value);
	}

	public function searchTrack($parameter)
	{
		// $api = Session::get('api');
		// $user_id = Session::get('user_id');
		// $search_query = Input::get('parameters');
		// foreach($search_query as $queries)
		// {
		// 	$tracks = $api->search($queries.song . ' ' . $queries.artist, 'track');
		// 	if(isset($tracks))
		// 	{
		// 		foreach($tracks->items as $singleTrack)
		// 		{
		// 			if($singleTrack.name == $song_name && $singleTrack->artist[0].name == $artist_name)
		// 			{
		// 				$api->addUserPlaylistTracks($user_id,$playlist_id, $singleTrack.id);
		// 				$songs_added = $songs_added + 1;
		// 			}

		// 		}
		// 	}
		// }
		$return_value = [];
		$return_value['songs_added'] = 1;
		echo json_encode($return_value);
	}

}
