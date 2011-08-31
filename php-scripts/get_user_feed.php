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
	$user_id = $_SESSION['user_id'];
	$twitterObj->setToken($token,$secret);
	
	//$friend_id = $_GET['friend_id'];
	//$user_id = $_GET['user_id'];
	$max_id = $_GET['max_id'];
	
	echo $formatted_max_id;
	$timeline_max = 50;
	if (!$max_id) $resp2 = $resp2 = $twitterObj->get('/statuses/friends_timeline.json',array('count'=>$timeline_max,'include_entities'=>'t','include_rts'=>'t'));
	else{
		$resp2 = $twitterObj->get('/statuses/friends_timeline.json',array('count'=>$timeline_max,'include_entities'=>'t','include_rts'=>'t','max_id'=>"$max_id"));
	}
	
	
	
	$response = $resp2->response;
	//print_r($response);
	if (!$max_id)$cur_index = 0;
	else $cur_index = 1;
	$cur_tweet = '';
	$url = '';
	$article = array('title'=>'','body'=>'null');
	while ($cur_index<count($response) && ($article['title']=='' || stristr($article['title'],'500 internal') || stristr($article['title'],'403 forbidden') || stristr($article['title'],'error 403') ||strlen($article['body'])<400 || $article['body']=='' | $article['title']=='Incompatible Browser')){
		$cur_tweet = $response[$cur_index];
		if ($cur_tweet['user']['id'] != $user_id){
			$text = $cur_tweet['text'];
			$url = $cur_tweet['entities']['urls'][0]['url'];
			//$regex = '$\b(https?)://[-A-Z0-9+&@#/%?=~_|!:,.;]*[-A-Z0-9+&@#/%=~_|]$i';
			//preg_match($regex, $text, $result);
			//$url = $result[0];
			if ($url) {
				$article = getArticleText($url);
			}
		}
		$cur_index++;
	}
	//echo print_r($response);
	//echo $url."<br>";
	//echo unshortenLink($url);
	//print_r(getArticleText($url));
	
	$tweet_id = $cur_tweet['id_str'];
	$tweet_user_name = $cur_tweet['user']['name'];
	$tweet_user_screenname = $cur_tweet['user']['screen_name'];
	$tweet_user_id = $cur_tweet['user']['id'];
	$tweet_user_image = $cur_tweet['user']['profile_image_url'];
	//$tweet_url = $cur_tweet['entities']['urls'][0]['expanded_url'];
	
	mysqlConnect();
	$res = mysql_query("SELECT id,short_url,permalink FROM links WHERE url = '$url'");
	$permalink = "";
	$short_url ="";
	if (mysql_num_rows($res)) {
		$row =mysql_fetch_array($res);
		$short_url = $row['short_url'];
		$link_id = $row['id'];
		$permalink = $row['permalink'];
	}
	else{
		$sql_title = mysql_real_escape_string($article['title']);
		$sql_body = mysql_real_escape_string($article['body'].$article['rest']);
		$sql_img = mysql_real_escape_string($article['image']);
			$permalink = getPermalink($article['title']);
		$sql_permalink = mysql_real_escape_string($permalink);
			$short_url = getShortUrl($permalink);
		$sql_short_url = mysql_real_escape_string($short_url);
		$res = mysql_query("INSERT INTO links (url,title,body_text,image_url,permalink,short_url) VALUES
		('$url','$sql_title','$sql_body','$sql_img','$sql_permalink','$sql_short_url')");
		$link_id = mysql_insert_id();
	}
	$res = mysql_query("INSERT INTO views (link_id,twitter_id) VALUES('$link_id','$tweet_user_id')");
	$tweet_res = mysql_query("SELECT id FROM tweets WHERE tweet_id='$tweet_id'");
	if (!mysql_num_rows($tweet_res)){	//if tweet does not exist in database
	$sql_tweet_text = mysql_real_escape_string($text);
	$res = mysql_query("INSERT INTO tweets (tweet_text,url_id,tweet_user_screenname,tweet_user_id,tweet_id,tweet_user_name,tweet_date,tweet_user_image_url) 
	VALUES('$sql_tweet_text','$link_id','$tweet_user_screenname','$tweet_user_id','$tweet_id','$tweet_user_name','','$tweet_user_image')");
	//echo "$tweet_id <br> $tweet_user_name <br> $tweet_user_screenname <br> $tweet_user_id <br> $tweet_user_image <br> $tweet_url";
	//print_r($cur_tweet['entities']);
	}
	
	$title = fixQuotes($article['title']);
	$tweet_button = "<a href=\"http://twitter.com/share\" class=\"twitter-share-button\"  
	data-text=\"Currently reading: $title\" data-url=\"http://ReadstreamApp.com/$permalink\" data-count=\"none\" data-via=\"ReadtreamApp\">Tweet</a>
	<script type=\"text/javascript\" src=\"http://platform.twitter.com/widgets.js\"></script>";
	$tweet_button = "<iframe allowtransparency=\"true\" frameborder=\"0\" scrolling=\"no\"
        src=\"http://platform.twitter.com/widgets/tweet_button.html?data-text='test'\"
        style=\"width:130px; height:50px;\"></iframe>";
	$fb_button = "<iframe src=\"http://www.facebook.com/plugins/like.php?href=http://ReadstreamApp.com/$permalink&amp;layout=button_count&amp;show_faces=false&amp;width=450&amp;action=like&amp;font&amp;colorscheme=light&amp;height=35\" 
	scrolling=\"no\" frameborder=\"0\" style=\"border:none; overflow:hidden; width:450px; height:35px;\" allowTransparency=\"true\"></iframe>";
		//$imageHtml = "<table><tr><td></td><td>$fb_button</td></tr></table>";
	$titleHtml = "<div id=\"arrow-right\"><a href='javascript:changePosition(position+1);' class='arrow'>next ></a></div>
			<div class=\"super-title\">
			 <div id=\"arrow-left\"></div>
			<div id=\"title\"><h1>{$article['title']}</h1></div>
			</div>";
			$alt_title = addslashes($article['title']);
			$realLinkHtml = "<a href='$url' alt=\"$alt_title\" target='_blank' id='full-article-link'>Read the original article</a>";
	if ($article['rest']){
			$teaserHtml = "<span id=\"teaser\">{$article['body']} <span class='read-more'><a href=\"javascript:readMore();\">Click here to read more...</a></span></span>";
			$restHtml = "<div class=\"hidden\" id='hidden'>{$article['rest']} <br>$realLinkHtml          or <a href='javascript:changePosition(position+1);'>next article ></a></div>";
		$bodyHtml = $teaserHtml.$restHtml;
	}
	else{
		$bodyHtml = "<span id=\"teaser\">{$article['body']} <br>$realLinkHtml          or <a href='javascript:changePosition(position+1);'>next article ></a></span>";
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
			<td><span class='retweet'><a href=\"javascript:retweet('$tweet_id',1);\">Retweet</a></span></td></tr></table>
			</div>"
				."</div></div>";
	
	$hiddenTweetHtml  = "<span id='permalink' style=\"display:none;\">/$permalink</span>
		<span id='short-url' style=\"display:none;\">$short_url</span>
		<span id=\"last-tweet\" style=\"display:none;\">$tweet_id</span>";
		$html ="<div id=\"main-box-stream\">".$titleHtml.$imageHtml.$bodyHtml."</div><div id=\"tweeter\">".$tweetDataHtml."</div>".$hiddenTweetHtml;
		
		echo $html;
	}
	catch (EpiTwitterServiceUnavailableException $e){
		echo "Looks like you've been busy reading!<br>Sorry! We do our best, but sometimes Twitter rate limits us or the Twitter Whale decides to come out and play...<br>
			Try refreshing, and if the page still doesn't work, come back in a few and try again! :)";
	}
	catch (EpiTwitterBadRequestException $e){
		echo "Looks like you've been busy reading!<br>Sorry! We do our best, but sometimes Twitter rate limits us or the Twitter Whale decides to come out and play...<br>
			Try refreshing, and if the page still doesn't work, come back in a few and try again! :)";
	}
	catch (EpiTwitterBadGatewayException $e){
		echo "Looks like you've been busy reading!<br>Sorry! We do our best, but sometimes Twitter rate limits us or the Twitter Whale decides to come out and play...<br>
			Try refreshing, and if the page still doesn't work, come back in a few and try again! :)";
	}
	catch (EpiTwitterNotAuthorizedException $e){
		//header("Location: http://tasteplug.com/readstream/");
		echo "Your user credentials have expired. Please <a href='http://ReadstreamApp.com/'>login</a> again! Sorry for the inconvenience.";
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
	
	function getArticleText($url){
		$teaser_num = 1000;
		mysqlConnect();
		$url = mysql_real_escape_string($url);
		$res = mysql_query("SELECT url,title,body_text,image_url FROM links WHERE (url='$url' OR short_url='$url') LIMIT 1");
		$title = '';
		$body_text = '';
		$rest = '';
		$image_url ='';
		if (mysql_num_rows($res)){
		//if web url is already cached
			$row = mysql_fetch_array($res);
			$title = $row['title'];
			$body_text = $row['body_text'];
			if (strlen($body_text)>$teaser_num){
					while (substr($body_text,$teaser_num-1,1) != ' '){
						$teaser_num++;
					}
				$rest = substr($body_text,$teaser_num);
				$body_text = substr($body_text,0,$teaser_num);
				
			}
			$image_url = $row['image_url'];
		}
		else{
		//if web page is not found in database
			$delim = "*^&^*";
			$result = shell_exec("/home/matchme/article-extract/run '$url' '$delim' 2>/dev/null");
			//$result = shell_exec("/home/rack/article-extract/run '$url' '$delim' 2>/dev/null");
			$article = explode($delim,$result);
			$title = $article[0];
				if ($title) str_replace('\n','',$title);
			$body_text = str_replace("\n","<p></p>",$article[1]);	
			
			
				if (strlen($body_text)>$teaser_num) {
					while (substr($body_text,$teaser_num-1,1) != ' '){
						$teaser_num++;
					}
					$rest = substr($body_text,$teaser_num);
					//$rest = 'TROLOL';
				}
				else $rest = '';
				
			$body_text = substr($body_text,0,$teaser_num);
			
			$image_url = $article[2];
		}
			return array('title'=>$title,'body'=>$body_text,'rest'=>$rest,'image'=>$image_url);
			//return array('title'=>"asdfda",'body'=>"asdfads");
	}
	function getPermalink($str){
		$str = preg_replace('/[^a-zA-Z0-9 ]/','',$str);
		$str = preg_replace('/(\s)+/','-',$str);
		//$arr = explode(' ',$str);
		//$str = implode('-',$arr);
		return strtolower($str);
	}
	
	function getShortUrl($permalink){
		$long_url = "http://readstreamapp.com/".$permalink;
		$long_url = urlencode($long_url);
		$api_key = "R_8c96ba4919fa2a995ea96380d4f9a678";
		$login = "wesleyzhao";
		$request_url = "http://api.bitly.com/v3/shorten?login=$login&apiKey=$api_key&longUrl=$long_url&format=json";
		$json = file_get_contents($request_url);
		$arr = json_decode($json,true);
		$data = $arr['data'];
		$url = $data['url'];
		return $url;
	}
	
	function fixQuotes($string){
	$string = str_replace("'",'&#39;',$string);
	return $string;
}
?>