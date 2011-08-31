<?php
require_once('mysql_connect.php');

session_start();
$user_id = $_SESSION['user_id'];
if ($user_id){
	$res = mysql_query("SELECT link_id FROM views WHERE twitter_id = '$user_id' ORDER BY 'id' DESC LIMIT 1");
//$res = mysql_query("SELECT id FROM views WHERE link_id='$link_id' AND twitter_id='$user_id'");
	if (mysql_num_rows($res)){
		$row = mysql_fetch_array($res);
		$link_id = $row['link_id'];
	
		$res = mysql_query("SELECT url,title,body_text FROM links WHERE id='$link_id'");
		$row = mysql_fetch_array($res);
		$url = $row['url'];
		$title = $row['title'];
		$body_text = $row['body_text'];
			$limit = 1000;
			$body = substr($body_text,0,$limit);
			if (strlen($body)>$limit) $rest = substr($body_text,$limit);
		$res = mysql_query("SELECT tweet_text,tweet_user_screenname,tweet_user_id,tweet_id,tweet_user_name FROM tweets WHERE url_id='$link_id'");
		$row = mysql_fetch_array($res);
		$tweet_text = $row['tweet_text'];
		$tweet_user_screenname  = $row['tweet_user_screenname'];
		$tweet_user_id = $row['tweet_user_id'];
		$tweet_id = $row['tweet_id'];
		$tweet_user_name = $row['tweet_user_name'];
	
			$titleHtml = "<h1>$title</h1>";
				$alt_title = addslashes($title);
				$realLinkHtml = "<a href='$url' alt=\"$alt_title\" target='_blank' id='full-article-link'>Read the original article.</a>";
		if ($rest){
				$teaserHtml = "<div id=\"teaser\">$body <span class='read-more'><a href=\"javascript:readMore();\">Click here to read more...</a></span></div>";
				$restHtml = "<div class=\"hidden\" id='hidden'>$rest <br>$realLinkHtml</div>";
			$bodyHtml = $teaserHtml.$restHtml;
		}
		else{
			$bodyHtml = "<div id=\"teaser\">$body <br>$realLinkHtml</div>";
		}
		$bodyHtml = "<div id=\"text\">".$bodyHtml."</div>";
		
		$tweetDataHtml = "<div id=\"tweeter-inside\">"."<div id=\"tweet-right\">".
			$text."<a href=\"javascript:unfollow($tweet_user_id);\" id=\"twit-link\">Unfollow</a> <a href=\"javascript:retweet($tweet_id);\" id=\"twit-link\">Retweet</a>".
			"<div id=\"author\">$tweet_user_name | <a href='http://twitter.com/$tweet_user_screenname' alt='$tweet_user_screenname Twitter profile' target='_blank'>@$tweet_user_screenname</alt></div>"."</div><img src=\"$tweet_user_image\" alt='$tweet_user_screenname Twitter profile picture' class='twitter-profile-image'></div>";
	
		$hiddenTweetHtml  = "<span id=\"last-tweet\" style=\"visibility: hidden;\">$tweet_id</span>";
			$html ="<div id=\"main-box-stream\">".$titleHtml.$bodyHtml."</div><div id=\"tweeter\">".$tweetDataHtml."</div>".$hiddenTweetHtml;
		
			echo $html;
	}
	else echo 'not found';
}
else{
	echo "stop trying to hack";
}
?>