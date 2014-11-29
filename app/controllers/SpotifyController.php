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

	public function searchTrack()
	{
		$api = Session::get('api');
		$user_id = Session::get('user_id');
		$playlist_id = Input::get('playlist_id');
		$search_query = Input::get('search_query');
		//$parameter = json_encode("{'playlist_id':'4SIpPAVWPb0I72ABqg9bhy','search_query':[{'song':'Guts over fear','artist':'eminem'},{'song':'beautiful','artist':'eminem'},{'song':'beautiful','artist':'smashing pumpkins'}]}");
		//$playlist_id = $parameter->playlist_id;
		//$search_query = $parameter->search_query;
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
		$return_value['songs_added'] = $search_query[0];
		echo json_encode($return_value);
	}

}
