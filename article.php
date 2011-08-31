<?php
session_start();
require_once('lib2/EpiCurl.php');
require_once('lib2/EpiOAuth.php');
require_once('lib2/EpiTwitter.php');
require_once('lib/secret.php');
require_once('php-scripts/mysql_connect.php');

$twitterObj = new EpiTwitter($consumer_key,$consumer_secret);

//if no token is set and no callback oath GET is set
	$auth_url = $twitterObj->getAuthorizationUrl();
	$token = $twitterObj->getRequestToken();
	$oauth = $token->oauth_token;

 $request = $_SERVER['REQUEST_URI'];
 $exploded = explode('/',$request);	//element 0 should be tasteplug.com
 $permalink = strtolower($exploded[1]);
 //echo $customURL;
 
 //$permalink = strtolower($_GET['p']);
 mysqlConnect();
 $query = "SELECT * FROM links WHERE permalink = '$permalink'";
 $res = mysql_query($query);
 if (mysql_num_rows($res)){
	$row = mysql_fetch_array($res);
	$body = $row['body_text'];
	$link_id = $row['id'];
	$url = $row['url'];
	$title = $row['title'];
	$image_url = $row['image_url'];
	$short_url = $row['short_url'];
	//echo "link id: $link_id";
	$res = mysql_query("SELECT tweet_text,tweet_user_screenname,tweet_user_image_url,tweet_user_name,tweet_user_id,tweet_id FROM tweets WHERE url_id='$link_id' LIMIT 1");
	$row = mysql_fetch_array($res);
	
	$tweet_user_screenname = $row['tweet_user_screenname'];
	$tweet_user_image = $row['tweet_user_image_url'];
	$tweet_user_name = $row['tweet_user_name'];
	$tweet_user_id = $row['tweet_user_id'];
	$tweet_id = $row['tweet_id'];
	$tweet_text = $row['tweet_text'];
	//echo $body;
	
	
	
	$titleHtml = "<h1>$title</h1>";
	$tweet_button = "<a href=\"http://twitter.com/share\" class=\"twitter-share-button\"  
	data-text=\"Currently reading: $title\" data-count=\"horizontal\" data-via=\"ReadtreamApp\">Tweet</a>
	<script type=\"text/javascript\" src=\"http://platform.twitter.com/widgets.js\"></script>";
	$fb_button = "<iframe src=\"http://www.facebook.com/plugins/like.php?href=http://readstreamApp.com/$permalink&amp;layout=standard&amp;show_faces=false&amp;width=450&amp;action=like&amp;font&amp;colorscheme=light&amp;height=35\" 
	scrolling=\"no\" frameborder=\"0\" style=\"border:none; overflow:hidden; width:450px; height:35px;\" allowTransparency=\"true\"></iframe>";
			$alt_title = addslashes($title);
			$realLinkHtml = "<a href='$url' alt=\"$alt_title\" target='_blank' id='full-article-link'>Read the original article.</a>";
	
		$bodyHtml = "<span id=\"teaser\">$body <br>$realLinkHtml</span>";
	$imageHtml = "<table><tr><td>$tweet_button</td><td>$fb_button</td></tr></table>";
	if (!strstr($image_url,'null')) $imageHtml = $imageHtml."<div id='article-image'><img class='article-image-class' src='$image_url' /></div>";
	$bodyHtml = "<div id=\"text\">".$bodyHtml."</div>";
		
	$tweetDataHtml = "<div id=\"tweeter-inside\">".
		"<div id='tweet-left'><a href='http://twitter.com/$tweet_user_screenname' alt='$tweet_user_screenname Twitter profile' target='_blank'><img src=\"$tweet_user_image\" alt='$tweet_user_screenname Twitter profile picture' class='twitter-profile-image'></a></div>".
		"<div id=\"tweet-right\">".
		$tweet_text."<div class='twit-link'>"
				."<table><tr><td><div id=\"author\">$tweet_user_name | <a href='http://twitter.com/$tweet_user_screenname' alt='$tweet_user_screenname Twitter profile' target='_blank'>@<span id='current-twitter-username'>$tweet_user_screenname</span>
				</div></td></tr></table>
			</div>"
				."</div></div>";
	
	$hiddenTweetHtml  = "<span id='permalink' style=\"visibility: hidden;\">/readstream/article.php?p=$permalink</span>
		<span id='short-url' style=\"visibility: hidden;\">$short_url</span>
		<span id=\"last-tweet\" style=\"visibility: hidden;\">$tweet_id</span>";
		$html ="<div id=\"main-box-stream\">".$titleHtml.$imageHtml.$bodyHtml."</div><div id=\"tweeter\">".$tweetDataHtml."</div>".$hiddenTweetHtml;
		
		//echo $html;
 }
 else{
	//echo $permalink."<br>$query";
	//echo $permalink;
	header("Location: 404.html");
}
?>
<!DOCTYPE html>
<html lang="en">   
<head>              
<meta charset="utf-8" />
<title><?=$title?> | Readstream - an intoxicating way to read what's relevant now</title> 
<link rel="stylesheet" type="text/css" href="style/style.css">                                                    
<script type="text/javascript">var _sf_startpt=(new Date()).getTime()</script>
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-12978591-8']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
<link rel="icon" href="favicon.ico" type="image/x-icon"> 
<link rel="shortcut icon" href="favicon.ico" type="image/x-icon"> 
<script type="text/javascript">var _sf_startpt=(new Date()).getTime()</script>
</head>                                         
<body>
	
	<div id="content">
	   
		<div id="top-stream">   
			<div id="right-bar">an intoxicating way to read what's relevant now</div>
			<div id="logo"><a href="/"><img src="images/logo.png"/ alt="Readstream"></a></div>
	    </div> 
		 <div id="timeline">
				<div id="timeline-top">
					<center>Login or Sign Up</center>
				</div> 
				<div class="login-bar">
				   <center>Get the most out of Readstream by clicking <a href="<?=$auth_url?>">here</a> to login or create an account.</center>
				</div>
		 </div>
		
		
			<!--<div id="main-box-stream">-->
				<!--<center>-->
					<?=$html?> 
				<!--</center>-->
		    <!--</div>-->
	    
	</div>                       
	
	<script type="text/javascript">
	var _sf_async_config={uid:19911,domain:"readstreamapp.com"};
	(function(){
	  function loadChartbeat() {
	    window._sf_endpt=(new Date()).getTime();
	    var e = document.createElement('script');
	    e.setAttribute('language', 'javascript');
	    e.setAttribute('type', 'text/javascript');
	    e.setAttribute('src',
	       (("https:" == document.location.protocol) ? "https://a248.e.akamai.net/chartbeat.download.akamai.com/102508/" : "http://static.chartbeat.com/") +
	       "js/chartbeat.js");
	    document.body.appendChild(e);
	  }
	  var oldonload = window.onload;
	  window.onload = (typeof window.onload != 'function') ?
	     loadChartbeat : function() { oldonload(); loadChartbeat(); };
	})();

	</script>	
</body>                                   
