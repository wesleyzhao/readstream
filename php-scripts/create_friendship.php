<?php

require_once('../lib2/EpiCurl.php');
require_once('../lib2/EpiOAuth.php');
require_once('../lib2/EpiTwitter.php');
require_once('../lib/secret.php');

$twitterObj = new EpiTwitter($consumer_key,$consumer_secret);

	session_start();
	$token = $_SESSION['ot'];
	$secret = $_SESSION['ots'];
	if ($token && $secret){
	$twitterObj->setToken($token,$secret);
	
	$friend_screen_name = $_GET['friend_screen_name'];
	
	$twitterObj->post('/friendships/create.json',array('screen_name'=>$friend_screen_name));
	}
	else{
		echo 'stop trying to hack';
	}
	//$twitterObj->post('/friendships/create.json',array('user_id'=>19829693));
	
?>