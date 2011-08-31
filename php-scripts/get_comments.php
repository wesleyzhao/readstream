<?php
require_once('mysql_connect.php');


$term = $_GET['term'];

	mysqlConnect();
	$res = mysql_query("SELECT short_url FROM links WHERE url='$term'");
	if (mysql_num_rows($res)){
		$row = mysql_fetch_array($res);
		$short_url = $row['short_url'];
		if ($short_url){
			$term = "$term+OR+".urlencode($short_url);
		}
	}
	//echo $term;
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
	$retweets = array();
	$total_count = count($all_results);
	if ($all_results){
		$all_results = array_reverse($all_results);
		//print_r($all_results);
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
			if (substr($result['text'],0,3) == 'RT '){
			//if this is an RT
				if ($retweets[$result['to_user_id']]){
					$retweets[$result['to_user_id']]['tweets_in_reply'][] = $cur_tweet;
				}	
				else if($retweets[$result['from_user_id_str']]){
					$retweets[$result['from_user_id_str']]['tweets_in_reply'][] = $cur_tweet;
				}
				else{
					$retweets[$result['from_user_id_str']] =$cur_tweet;
				}
			}
			else{
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
		}
		
		//echo "Total results: $total_count <br>"; 
		//print_r($tweets);
		$html = "
			<h3>Comments ($total_count)</h3>";
		$html = $html.makeComments($tweets).makeComments($retweets);
		echo $html;
	}
	else echo "no recent comments found";
	
	
	function makeComments($tweets){
		$html = '';
		foreach ($tweets as $tweet_id => $value){
			$html = $html."<div class=\"comment-super\" id=\"{$value['tweet_id']}\">";
			$imageHtml = "<div class=\"comment-image-div\"><img class=\"comment-image\" alt = '{$value['user_name']} Twitter profile picture' src=\"{$value['profile_image']}\"></div>";
			$authorHtml = "<div class=\"author-name\">
							<a href=\"http://twitter.com/{$value['user_name']}\">@{$value['user_name']}</a>
						</div>";
			$socialHtml = "	<div class=\"social\">
							<div class=\"comm-link\"><a id ='retweet-{$value['tweet_id']}' href=\"javascript:retweet('{$value['tweet_id']}');\">Retweet</a> <a href=\"javascript:makeReply('{$value['user_name']}','{$value['tweet_id']}')\">Reply</a></div>
						</div>";
			$commentTextHtml = "<div class=\"comment-text\">$authorHtml".$value['text'].$socialHtml."</div>";
			$commentReg = "<div class=\"comment\">".$imageHtml.$commentTextHtml."</div>";
			$replyHtml = '';
			if (count($value['tweets_in_reply'])>0){
				foreach($value['tweets_in_reply'] as $reply){
					$replyHtml = $replyHtml."<div class=\"reply\" id=\"{$reply['tweet_id']}\">";
					$imageHtml = "<div class=\"reply-image-div\"><img class=\"reply-image\" alt = '{$reply['user_name']} Twitter profile picture' src=\"{$reply['profile_image']}\"></div>";
					$authorHtml = "<div class=\"author-name\">
							<a href=\"http://twitter.com/{$reply['user_name']}\">@{$reply['user_name']}</a>
							</div>";
					$socialHtml = "	<div class=\"social\">
							<div class=\"comm-link\"><a id ='retweet-{$reply['tweet_id']}' href=\"javascript:retweet('{$reply['tweet_id']}');\">Retweet</a> <a href=\"javascript:makeReply('{$reply['user_name']}','{$reply['tweet_id']}')\">Reply</a></div>
						</div>";
					$replyTextHtml = "<div class=\"reply-text\">$authorHtml".$reply['text'].$socialHtml."</div>";
					$replyHtml = $replyHtml.$imageHtml.$replyTextHtml."</div>";
				}
			}
			$html = $html.$commentReg.$replyHtml."</div>";
		}
		return $html;
	}
?>