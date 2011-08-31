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
	
		$friend_id = $_GET['friend_id'];
	
		$twitterObj->post('/friendships/destroy.json',array('user_id'=>$friend_id));
	}
	else{
		echo "stop trying to hack!";
	}
	
	
?>