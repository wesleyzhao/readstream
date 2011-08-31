<?php
	$long_url = $_GET['url'];
	$long_url = urlencode($long_url);
	$api_key = "R_8c96ba4919fa2a995ea96380d4f9a678";
	$login = "wesleyzhao";
	$request_url = "http://api.bitly.com/v3/shorten?login=$login&apiKey=$api_key&longUrl=$long_url&format=json";
	$json = file_get_contents($request_url);
	$arr = json_decode($json,true);
	$data = $arr['data'];
	$url = $data['url'];
	
	echo $url;

?>