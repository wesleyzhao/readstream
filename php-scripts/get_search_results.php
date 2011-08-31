<?php

$term = $_GET['term'];
	$page = 1;
	$all_results = array();
	while ($page ==1 || $results){
		$url = "http://search.twitter.com/search.json?q=$term&page=$page";
		$json = file_get_contents($url);
		$arr = json_decode($json,true);
		//print_r($arr);
		$results = $arr['results'];
		if ($results) $all_results = array_merge($all_results,$results);
		$page=$page+1;
	}
	$tweets = array();
	$total_count = count($all_results);
	if ($all_results){
		$all_results = array_reverse($all_results);
		foreach ($all_results as $result){
			$cur_tweet  = array(
				'user_id'=>$result['from_user_id_str'],
				'user_name'=>$result['from_user'],
				'profile_image'=>$result['profile_image_url'],
				'tweet_id'=>$result['id_str'],
				'text'=>$result['text'],
				'reply_to_user_id'=>$result['to_user_id'],
				'tweets_in_reply'=>array()
				);
			if ($tweets[$result['to_user_id']]){
				$tweets[$result['to_user_id']]['tweets_in_reply'][] = $cur_tweet;
			}	
			else if($tweets[$result['from_user_id_str']]){
				$tweets[$result['from_user_id_str']]['tweets_in_reply'][] = $cur_tweet;
			}
			else{
				$tweets[$result['from_user_id_str']] =$cur_tweet;
			}
		}
		echo "Total results: $total_count <br>"; 
		//print_r($tweets);
	}
	else echo "no recent comments found";
	
?>