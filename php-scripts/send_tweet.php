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
	
		$tweet_id = $_GET['tweet_id'];
		$text = $_GET['tweet_text'];
		
		$twitterObj->post('/statuses/update.json',array('status'=>$text,'in_reply_to_status_id'=>$tweet_id));
	}
	else{
		echo "stop trying to hack!";
	}
?>