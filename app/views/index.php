<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Music On My Bussiness</title>
	<style>
</head>
<body>
 	<?php
			if (isset($_GET['code'])) {
				Session::put('authCode',$_GET['code']);
				$session = Session::get("spotifySession");
				$api = Session::get("api");
			    $session->requestToken(Session::get("authCode"));
			   	$api->setAccessToken($session->getAccessToken());
			    print_r($api->me());

		    }
	?> 
</body>
</html>
