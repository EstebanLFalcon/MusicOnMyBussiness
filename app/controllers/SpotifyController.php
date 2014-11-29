<?php

class SpotifyController extends BaseController {

	public function login()
	{
		$session = new SpotifyWebAPI\Session('78fe14946efe45f285d840b72bea40e4', '54db8e2ab9204bc08d09da67577c3bee', 'http://localhost:8000/');
		$api = new SpotifyWebAPI\SpotifyWebAPI();
		$code = $session->getAuthorizeUrl(array('scope' => array('user-read-email', 'user-library-modify','user-read-private','playlist-read-private','playlist-modify-private','user-library-modify','user-read-email','playlist-modify-public','user-library-read','user-read-private')),true);
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
		$song = Input::get('song');
		$artist = Input::get('artist');
		$songs_added = 0;
		$tracks = $api->search($song . ' ' . $artist, 'track');
		if(isset($tracks))
		{
			foreach($tracks->tracks->items as $singleTrack)
			{
			//	if($singleTrack->name == $song && $singleTrack->artist[0]->name == $artist)
			//	{
					$api->addUserPlaylistTracks($user_id,$playlist_id, $singleTrack->id);
					$songs_added = $songs_added + 1;
					break;
				//}
			}
			//$api->addUserPlaylistTracks($user_id,$playlist_id, $tracks->tracks->items[0]->id;)
		}
		$return_value = [];
		$return_value['songs_added'] = $songs_added;

		echo json_encode($return_value);
	}

}
