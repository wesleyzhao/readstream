<?php

require_once('../lib2/EpiCurl.php');
require_once('../lib2/EpiOAuth.php');
require_once('../lib2/EpiTwitter.php');
require_once('../lib/secret.php');
require_once('mysql_connect.php');
try{
$twitterObj = new EpiTwitter($consumer_key,$consumer_secret);

	session_start();
	$token = $_SESSION['ot'];
	$secret = $_SESSION['ots'];
	$twitterObj->setToken($token,$secret);
	
	//$friend_id = $_GET['friend_id'];
	//$user_id = $_GET['user_id'];
	$max_id = $_GET['max_id'];
	
	echo $formatted_max_id;
	$timeline_max = 200;
	if (!$max_id) $resp2 = $resp2 = $twitterObj->get('/statuses/friends_timeline.json',array('count'=>$timeline_max,'include_entities'=>'t','include_rts'=>'t'));
	else{
		$resp2 = $twitterObj->get('/statuses/friends_timeline.json',array('count'=>$timeline_max,'include_entities'=>'t','include_rts'=>'t','max_id'=>"$max_id"));
	}
	
	
	
	$response = $resp2->response;
	//UNCOMMENT WHEN DONE
	/*
	//print_r($response);
	if (!$max_id)$cur_index = 0;
	else $cur_index = 1;
	$cur_tweet = '';
	$url = '';
	$article = array('title'=>'','body'=>'null');
	while ($cur_index<count($response) && ($article['title']=='' || stristr($article['title'],'403 forbidden') || stristr($article['title'],'error 403') ||strlen($article['body'])<400 || $article['body']=='' | $article['title']=='Incompatible Browser')){
		$cur_tweet = $response[$cur_index];
		$text = $cur_tweet['text'];
		$url = $cur_tweet['entities']['urls'][0]['url'];
		//$regex = '$\b(https?)://[-A-Z0-9+&@#/%?=~_|!:,.;]*[-A-Z0-9+&@#/%=~_|]$i';
		//preg_match($regex, $text, $result);
		//$url = $result[0];
		if ($url) $article = getArticleText($url);
		$cur_index++;
	}
	*/
	$ranked = array();
	foreach ($response as $tweet_response){
		$url = $tweet_response['entities']['urls'][0]['url'];
		if ($url){
		//if tweet include $url
			if ($ranked[$url]){
				$ranked[$url]['tweets'][] = $tweet_response;
				$ranked[$url]['count'] = $ranked[$url]['count']+1;
			}
			else{
				$ranked[$url] = array('tweets'=>array(),'count'=>1);
				$ranked[$url]['tweets'][] = $tweet_response;
			}
		}
	}
	uasort($ranked,"cmp");
	print_r($ranked);
	//echo print_r($response);
	//echo $url."<br>";
	//echo unshortenLink($url);
	//print_r(getArticleText($url));
	
	//UNCOMMENT WHEN DONE
	/*	
	$tweet_id = $cur_tweet['id_str'];
	$tweet_user_name = $cur_tweet['user']['name'];
	$tweet_user_screenname = $cur_tweet['user']['screen_name'];
	$tweet_user_id = $cur_tweet['user']['id'];
	$tweet_user_image = $cur_tweet['user']['profile_image_url'];
	//$tweet_url = $cur_tweet['entities']['urls'][0]['expanded_url'];
	
	mysqlConnect();
	$res = mysql_query("SELECT id FROM links WHERE url = '$url'");
	if (mysql_num_rows($res)) {
		$row =mysql_fetch_array($res);
		$link_id = $row['id'];
	}
	else{
		$sql_title = mysql_real_escape_string($article['title']);
		$sql_body = mysql_real_escape_string($article['body'].$article['rest']);
		$sql_img = mysql_real_escape_string($article['image']);
		$res = mysql_query("INSERT INTO links (url,title,body_text,image_url) VALUES
		('$url','$sql_title','$sql_body','$sql_img')");
		$link_id = mysql_insert_id();
	}
	$res = mysql_query("INSERT INTO views (link_id,twitter_id) VALUES('$link_id','$tweet_user_id')");
	$tweet_res = mysql_query("SELECT id FROM tweets WHERE tweet_id='$tweet_id'");
	if (!mysql_num_rows($tweet_res))	//if tweet does not exist in database
	$res = mysql_query("INSERT INTO tweets (tweet_text,url_id,tweet_user_screenname,tweet_user_id,tweet_id,tweet_user_name,tweet_date) 
	VALUES('$text','$link_id','$tweet_user_screenname','$tweet_user_id','$tweet_id','$tweet_user_name','')");
	//echo "$tweet_id <br> $tweet_user_name <br> $tweet_user_screenname <br> $tweet_user_id <br> $tweet_user_image <br> $tweet_url";
	//print_r($cur_tweet['entities']);
	
	

	$titleHtml = "<h1>{$article['title']}</h1>";
			$alt_title = addslashes($article['title']);
			$realLinkHtml = "<a href='$url' alt=\"$alt_title\" target='_blank' id='full-article-link'>Read the original article.</a>";
	if ($article['rest']){
			$teaserHtml = "<div id=\"teaser\">{$article['body']} <span class='read-more'><a href=\"javascript:readMore();\">Click here to read more...</a></span></div>";
			$restHtml = "<div class=\"hidden\" id='hidden'>{$article['rest']} <br>$realLinkHtml</div>";
		$bodyHtml = $teaserHtml.$restHtml;
	}
	else{
		$bodyHtml = "<div id=\"teaser\">{$article['body']} <br>$realLinkHtml</div>";
	}
	if (!strstr($article['image'],'null')) $imageHtml = "<div id='article-image'><img class='article-image-class' src='{$article['image']}' /></div>";
	$bodyHtml = "<div id=\"text\">".$bodyHtml."</div>";
		
	$tweetDataHtml = "<div id=\"tweeter-inside\">".
		"<div id='tweet-left'><a href='http://twitter.com/$tweet_user_screenname' alt='$tweet_user_screenname Twitter profile' target='_blank'><img src=\"$tweet_user_image\" alt='$tweet_user_screenname Twitter profile picture' class='twitter-profile-image'></a></div>".
		"<div id=\"tweet-right\">".
		$text."<div class='twit-link'>"
				."<table><tr><td><div id=\"author\">$tweet_user_name | <a href='http://twitter.com/$tweet_user_screenname' alt='$tweet_user_screenname Twitter profile' target='_blank'>@<span id='current-twitter-username'>$tweet_user_screenname</span>
				</div></td>
				<td><span class='unfollow'><a href=\"javascript:unfollow('$tweet_user_id');\">Unfollow</a></span></td>
			<td><span class='retweet'><a href=\"javascript:retweet('$tweet_id');\">Retweet</a></span></td></tr></table>
			</div>"
				."</div></div>";
	
	$hiddenTweetHtml  = "<span id=\"last-tweet\" style=\"visibility: hidden;\">$tweet_id</span>";
		$html ="<div id=\"main-box-stream\">".$titleHtml.$imageHtml.$bodyHtml."</div><div id=\"tweeter\">".$tweetDataHtml."</div>".$hiddenTweetHtml;
		
		echo $html;
		*/
	}
	catch (EpiTwitterServiceUnavailableException $e){
		echo "You have eliminated a lot of friends! The Grim Tweeper is tired... come back in about an hour.<br><br><em>AKA: Twitter rate limited us... or the Twitter Whale is at it again :(</em>";
	}
	catch (EpiTwitterBadRequestException $e){
		echo "You have eliminated a lot of friends! The Grim Tweeper is tired... come back in about an hour.<br><br><em>AKA: Twitter rate limited us... or the Twitter Whale is at it again :(</em>";
	}
	catch (EpiTwitterBadGatewayException $e){
		echo "You have eliminated a lot of friends! The Grim Tweeper is tired... come back in about an hour.<br><br><em>AKA: Twitter rate limited us... or the Twitter Whale is at it again :(</em>";
	}
	//print_r($resp);
	
	function unshortenLink($url){
		$ch = curl_init();  
		curl_setopt($ch, CURLOPT_URL, "$url");  
		curl_setopt($ch, CURLOPT_HEADER, 1);  
		curl_setopt($ch, CURLOPT_NOBODY, 1);  
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);  
  
		$result = curl_exec($ch);   
	
		if (preg_match("/Location\:/","$result")) {  
			$url = explode("Location: ",$result);  
			$reversed_url = explode("\r",$url[1]);  
			return $reversed_url[0];  
		} 
		else {  
			return "url error";  
		}  
	}
	
	function cmp($rank_el_a,$rank_el_b){
		if ($rank_el_a['count']==$rank_el_b['count']){
			return 0;
		}
		return ($rank_el_a['count'] < $rank_el_b['count']) ? 1 :-1;
	}
	
	function getArticleText($url){
		$teaser_num = 1000;
		mysqlConnect();
		$url = mysql_real_escape_string($url);
		$res = mysql_query("SELECT title,body_text,image_url FROM links WHERE url='$url'");
		if (mysql_num_rows($res)){
		//if web url is already cached
			$row = mysql_fetch_array($res);
			$title = $row['title'];
			$body_text = $row['body_text'];
			if (strlen($body_text)>$teaser_num){
				$rest = substr($body_text,$teaser_num);
				$body_text = substr($body_text,0,$teaser_num);
				
			}
			$image_url = $row['image_url'];
		}
		else{
		//if web page is not found in database
			$delim = "*^&^*";
			//$result = shell_exec("/home/matchme/article-extract/run '$url' '$delim' 'fast' 2>/dev/null");
			$result = shell_exec("/home/matchme/article-extract/run '$url' '$delim' 2>/dev/null");
			$article = explode($delim,$result);
			$title = $article[0];
				if ($title) str_replace('\n','',$title);
			$body_text = str_replace("\n","<p></p>",$body_text);	
			$body_text = substr($article[1],0,$teaser_num);
			
				if (strlen($body_text)>$teaser_num) $rest = substr($article[1],$teaser_num);
				else $rest = '';
			
			$image_url = $article[2];
		}
			return array('title'=>$title,'body'=>$body_text,'rest'=>$rest,'image'=>$image_url);
			//return array('title'=>"asdfda",'body'=>"asdfads");
	}
?>