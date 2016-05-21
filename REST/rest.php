<?php

	//https://maps.googleapis.com/maps/api/geocode/json?address=disneyland,%20ca

	$locate = isset($_GET['location']) ? $_GET['location'] : "";
	if (!empty($locate)) {
		$maps_url = 'https://maps.googleapis.com/maps/api/geocode/json?address='.urlencode($locate);
		echo $maps_url;

		$maps_json = file_get_contents($maps_url);
		var_dump($maps_json);
		$maps_array = json_decode($maps_json, true);

		$lat = $maps_array['results'][0]['geometry']['location']['lat'];
		$lng = $maps_array['results'][0]['geometry']['location']['lng'];

		echo "<br>lat:$lat lng:$lng";
		$instagram_url = 'https://api.instagram.com/v1/media/search?lat='.$lat.
			'&lng='.$lng.'&access_token=3236616963.ff13a35.0771a6b9c86b469d93cb2151b30525ff';
		echo '<br>'.$instagram_url;

		$instagram_json = file_get_contents($instagram_url);
		var_dump($instagram_json);
		$instagram_array = json_decode($instagram_json, true);
	}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8"/>
	<title>geogram</title>
</head>
<body>
<form action="">
	<input type="text" name="location" value="disneyland, ca"/>
	<button type="submit">submit</button>
</form>
</body>
</html>



