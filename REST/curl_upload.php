<?php

	/* Веб-сервис работы с API транспортной компании "Деловые линии" */

	//формируем JSON запрос для отправки на сервер транспортной компании
	$arr = array(
		"derivalPoint" => "7800000000000000000000000",
		"derivalDoor" => true, 
		"arrivalPoint" => "5200000100000000000000000", 
		"arrivalDoor" => false, 
		"sizedVolume" => 1, 
		"sizedWeight" => 1,
		"statedValue" => 0
	);
	$json_str=json_encode($arr);

	$url = 'https://api.dellin.ru/v1/public/calculator.json';

	$session = curl_init();
	// Set curl options
	curl_setopt($session, CURLOPT_URL, $url);
	curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
	//отправка JSON-файла с указанием заголовка application/json методом POST
	curl_setopt($session, CURLOPT_POST, 1);
	curl_setopt($session, CURLOPT_POSTFIELDS, $json_str);
	curl_setopt($session, CURLOPT_HTTPHEADER, array
		(
			'Content-Type: application/json',
			'Content-Length: '.strlen($json_str))
		);
	// Make the request
	$response = curl_exec($session);
	// Close the curl session
	curl_close($session);

	if ($response === FALSE) {
		echo "Error cURL: " . curl_error($session);
	}
	
	echo "<br>response: ".$response."<br>";
	$output =  json_decode($response,true);
	var_dump($output);
?>
