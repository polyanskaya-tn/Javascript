<?php

	$locate = isset($_GET['location']) ? $_GET['location'] : "";
	if (!empty($locate)) {
		$url = 'https://maps.googleapis.com/maps/api/geocode/json?address='.urlencode($locate);

		$session = curl_init();
		// Set curl options
		curl_setopt($session,CURLOPT_URL,$url);
		curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
		// Make the request
		$response = curl_exec($session);
		// Close the curl session
		curl_close($session);

		$maps_array =  json_decode($response,true);
		$lat = $maps_array['results'][0]['geometry']['location']['lat'];
		$lng = $maps_array['results'][0]['geometry']['location']['lng'];

		$url = 'https://api.vk.com/method/photos.search?lat='.$lat.
			'&long='.$lng;

		$session1 = curl_init();
		// Set curl options
		curl_setopt($session1,CURLOPT_URL,$url);
		curl_setopt($session1, CURLOPT_RETURNTRANSFER, true);
		// Make the request
		$response1 = curl_exec($session1);
		// Close the curl session
		curl_close($session1);
		$foto_array =  json_decode($response1,true);
	}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8"/>
	<title>Find foto</title>
	<style>
		img {
			float: left;
			margin: 20px;
			height: 150px;
		};
	</style>
</head>
<body>
<form action="">
	<input type="text" name="location"/>
	<button type="submit">Find foto</button>
</form>
	<?
		if (isset($foto_array)) 
			foreach ($foto_array['response'] as $foto) {
				if (isset($foto['src']))
					echo '<img src="'.$foto['src'].'">';
			}
	?>
</body>
</html>
