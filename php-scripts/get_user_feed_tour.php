<?php

require_once('mysql_connect.php');
try{
	
	//$friend_id = $_GET['friend_id'];
	//$user_id = $_GET['user_id'];
	$max_id = $_GET['max_id'];
	
	mysqlConnect();
	if ($max_id){
		$res = mysql_query("SELECT id,tweet_text,url_id,tweet_user_screenname,tweet_user_id,tweet_id,tweet_user_name,tweet_user_image_url FROM tweets WHERE id<'$max_id' ORDER BY id DESC LIMIT 1");
	}
	else{
		$res = mysql_query("SELECT id,tweet_text,url_id,tweet_user_screenname,tweet_user_id,tweet_id,tweet_user_name,tweet_user_image_url FROM tweets ORDER BY id DESC LIMIT 1");
	}
	$row = mysql_fetch_array($res);
	$tweet_table_id = $row['id'];
	$url_id = $row['url_id'];
			
		$tweet_id = $row['tweet_id'];
		$tweet_user_name = $row['tweet_user_name'];
		$tweet_user_screenname = $row['tweet_user_screenname'];
		$tweet_user_id = $row['tweet_user_id'];
		$tweet_user_image = $row['tweet_user_image_url'];
		$text = $row['tweet_text'];
	//$tweet_url = $cur_tweet['entities']['urls'][0]['expanded_url'];
	
	$res2 = mysql_query("SELECT * FROM links WHERE id = '$url_id'");
	$row2 = mysql_fetch_array($res2);
		$url = $row2['url'];
		$article = array();
		$article['title'] =  $row2['title'];
		$article['body'] = $row2['body_text'];
		$article['rest'] = '';
		$article['image'] = $row2['image_url'];
		$permalink = $row2['permalink'];
		$short_url = $row2['short_url'];
		$teaser_num = 1000;
			if (strlen($article['body'])>$teaser_num) {
				
				while (substr($article['body'],$teaser_num-1,1) != ' '){
					$teaser_num++;
				}
				$article['rest'] = substr($article['body'],$teaser_num);
				$article['body'] = substr($article['body'],0,$teaser_num);
			}

	$titleHtml = "<div id=\"arrow-right\"><a href='javascript:changePosition(position+1);' class='arrow'>next ></a></div>
			<div class=\"super-title\">
			 <div id=\"arrow-left\"></div>
			<div id=\"title\"><h1>{$article['title']}</h1></div>
			</div>";
			$alt_title = addslashes($article['title']);
			$realLinkHtml = "<a href='$url' alt=\"$alt_title\" target='_blank' id='full-article-link'>Read the original article</a>";
	if ($article['rest']){
			$teaserHtml = "<span id=\"teaser\">{$article['body']} <span class='read-more'><a href=\"javascript:readMore();\">Click here to read more...</a></span></span>";
			$restHtml = "<div class=\"hidden\" id='hidden'>{$article['rest']} <br>$realLinkHtml           or <a href='javascript:changePosition(position+1);'>next article ></a></div>";
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
				<td><span class='unfollow'>Unfollow</span></td>
			<td><span class='retweet'>Retweet</span></td></tr></table>
			</div>"
				."</div></div>";
	
	$hiddenTweetHtml  = "<span id='permalink' style=\"display:none;\">/$permalink</span>
		<span id='short-url' style=\"display:none;\">$short_url</span>
		<span id=\"last-tweet\" style=\"display:none;\">$tweet_table_id</span>";
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
	
	

?>