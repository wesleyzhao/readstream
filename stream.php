<?php
session_start();
require_once('lib2/EpiCurl.php');
require_once('lib2/EpiOAuth.php');
require_once('lib2/EpiTwitter.php');
require_once('lib/secret.php');

if (!($_SESSION['ot'] && $_SESSION['ots'])) header ("Location: http://readstreamapp.com/");
else{
		$twitterObj = new EpiTwitter($consumer_key,$consumer_secret);
		$twitterObj->setToken($_SESSION['ot'],$_SESSION['ots']);
}
	try{
	$twitterInfo = $twitterObj->get_accountVerify_credentials();
	}
	catch (EpiTwitterNotAuthorizedException $e){
		header("Location: http://readstreamapp.com/");
	}
	catch (EpiTwitterBadRequestException $e){
		echo "Looks like you've been busy reading!<br>Sorry! We do our best, but sometimes Twitter rate limits us or the Twitter Whale decides to come out and play...<br>
			Try refreshing, and if the page still doesn't work, come back in a few and try again! :)";
	}
	$friends_count = intval($twitterInfo->friends_count);
	if ($friends_count <15 ){
		//$_SESSION['more-followers'] = 1;
		if (!$_SESSION['more-followers'])	header("Location: more-followers.php");
	}
?>
<!DOCTYPE html>
<html lang="en">   
<head>              
<meta charset="utf-8" />
<title>ReadStream | an intoxicating way to read what's relevant now</title> 
<link rel="stylesheet" type="text/css" href="style/style.css">
<script type="text/javascript"
 src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js">
</script> 
<script type="text/javascript" src="js/stream.js"></script>  
<script type="text/javascript">var _sf_startpt=(new Date()).getTime()</script>
<link rel="icon" href="favicon.ico" type="image/x-icon"> 
<link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
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
<script type="text/javascript">var _sf_startpt=(new Date()).getTime()</script> 
</head>                                         
<body>
	
	<div id="content">
	   
		<div id="top-stream">   
			<div id="right-bar">an intoxicating way to read what's relevant now | <a href="logout.php">logout</a></div>
			<div id="logo">
				<a href="/"><img src="images/logo.png"/ alt="Readstream"></a>
				<div class = 'stream-twitter-button' id = 'twitter-button'><a href="http://twitter.com/share" class="twitter-share-button" data-url="http://ReadstreamApp.com" data-text="Enjoying reading on Readstream - a better way to consume all your Twitter links" data-count="horizontal" data-via="ReadstreamApp">Tweet</a><script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script></div>
			</div> 
	    </div>            
		<div id="timeline-super">
				<div id="timeline">
					<div id="timeline-top">
						<center>Your stream</center>
					</div> 
				</div>
				<div class="loading-hidden">
				   <center><img src="images/article-loading.gif"/></center>
				</div>
		 </div>
		<div id="replace">
			<div id="main-box-stream">
				<center>
					 <h2>Your content is loading. Please wait...</h2>
					 <img src="images/arrows.png">
					 <h2>Use your arrow keys to navigate.</h2>
					 <br>
					
				</center>
		    </div>
		</div>
		 
		
		<div class="hideme">
		<textarea class="tweet" id="tweet" onFocus = "twitReplace()">Leave a Tweet</textarea>
		<div id="twit-container">
		<input type="image" src="images/tweet.png" class="tweet-button" onclick="javascript:sendTweet();">
		<div id="chars-count">140 characters left</div>
		</div>
		<br> 
		<div id="comments">
			<h3>Comments</h3>
			<div class="comment-super" id="1234">
				<div class="comment">
					<div class="comment-image-div">
						<img class="comment-image" src="images/twimage.png">
					</div>                          
					<div class="comment-text">
						<div class="author-name">
							<a href="http://twitter.com/danshipper">@danshipper</a>
						</div>
						this is such a cool story! yea man sooper fucking cool stuff i really love all of this dude really really sweet dude
						<div class="social">
							<div class="comm-link"><a href="#">Retweet</a> <a href="#">Reply</a></div>
						</div>
					</div>
				</div>
				<div class="reply" id="4567">
				   	<div class="reply-image-div">
						<img class="reply-image" src="images/twimage.png">
					</div>                          
					<div class="reply-text">
						<div class="author-name">
							<a href="http://twitter.com/danshipper">@danshipper</a>
						</div>
						this is such a cool story! yea man sooper fucking cool stuff i really love all of this dude really really sweet dude
						<div class="social">
							<div class="comm-link"><a href="#">Retweet</a> <a href="#">Reply</a></div>
						</div>
					</div>
				</div>
			</div> 
			
				
		</div>
		</div> 
		<div class="reply-hide"></div>
		
	</div>                       
	<script type="text/javascript">
document.write('<scr' + 'ipt src="' + document.location.protocol + '//fby.s3.amazonaws.com/fby.js"></scr' + 'ipt>');
</script>
<script type="text/javascript">
FBY.showTab({id: '712', position: 'left', color: '#D44361'});
</script>
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
