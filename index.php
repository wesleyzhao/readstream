<?php

session_start();
require_once('lib2/EpiCurl.php');
require_once('lib2/EpiOAuth.php');
require_once('lib2/EpiTwitter.php');
require_once('lib/secret.php');
require_once('php-scripts/mysql_connect.php');

$twitterObj = new EpiTwitter($consumer_key,$consumer_secret);
$oauth_token = $_GET['oauth_token'];
if ($oauth_token == '' && $_SESSION['ot']==''){
//if no token is set and no callback oath GET is set
	$url = $twitterObj->getAuthorizationUrl();
	$token = $twitterObj->getRequestToken();
	$oauth = $token->oauth_token;
}
else{
	//$_SESSION['oauth_token'] = $_GET['oauth_token'];
	if ($_SESSION['ot']=='' && $_SESSION['ots']==''){
		//if callback is sent, but not stored yet
		$twitterObj->setToken($_GET['oauth_token']);
		$token = $twitterObj->getAccessToken();
		$twitterObj->setToken($token->oauth_token,$token->oauth_token_secret);
		$_SESSION['ot'] = $token->oauth_token;
		$_SESSION['ots'] = $token->oauth_token_secret;
		$twitterInfo = $twitterObj->get_accountVerify_credentials();
		$username = $twitterInfo->screen_name;
		$user_id =$twitterInfo->id;
			$_SESSION['user_id'] = $user_id;
		$profilepic = $twitterInfo->profile_image_url;
		$description = addslashes($twitterInfo->description);
		$name = $twitterInfo->name;
		$query = "INSERT INTO users (twitter_id,name,description,oauth_token,oauth_token_secret,profile_image_url,username)
				VALUES('$user_id','$name','$description','{$_SESSION['ot']}','{$_SESSION['ots']}','$profilepic','$username')";
		mysqlConnect();
		$res = mysql_query("SELECT id FROM users WHERE twitter_id='$user_id'");
		if (!mysql_num_rows($res)){
			mysql_query($query);
		}
	}
	else{
		$twitterObj->setToken($_SESSION['ot'],$_SESSION['ots']);
		//session_destroy();
	}
	header("location: stream.php");
	
	
	}

?>
<!DOCTYPE html>
<html lang="en">   
<head>              
<meta charset="utf-8" />
<title>ReadStream | an intoxicating way to read what's relevant now</title> 
<link rel="stylesheet" type="text/css" href="style/style.css">  
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
	
	<div id="content-index">
		<div id="top">
			<div id="right-bar"><a href="<?=$url?>" alt="Log-in to Readstream | Socially curated stream of links, reddit killer">login</a> or <a href="<?=$url?>" alt="Sign up for Readstream | Socially curated stream of links, reddit killer">sign up</a></div>
			<div id="logo"><a href="/"><img src="images/logo.png"/ alt="Readstream"></a></div> 
			<!-- <div id="plug"> <a href="http://techcrunch.com/2011/04/21/yc_y_u_no-give-twitter-link-readers-auto-entry-into-yc/">Featured on <img src="http://upload.wikimedia.org/wikipedia/en/4/43/Techcrunch-transparent-photshop.gif"></a> </div> -->
	    </div>
		<div id="main-box">
			<center><h1>Watch out, you're about to be addicted.</h1></center>
			<div id="description">
				<h2>Ever wanted a fully featured, socially curated
				stream of all the links brought to you by 
				your Twitter timeline?<br><br>	 

				You're in luck because we just built it!!</h2> 
				<center><a href="<?=$url?>" alt="Sign up for Readstream | Socially curated stream of links, reddit killer"><img src="images/signup.png"/></a></center>
				<div id="disclaimer"><center><a href="/tour.php">Or take a tour</a> | We don't spam and never will.</center></div>
			</div>
			<img src="images/screenshot.png" id="screenshot"/>
		</div>
		<center><div id="plug"> <a href="http://techcrunch.com/2011/04/21/yc_y_u_no-give-twitter-link-readers-auto-entry-into-yc/">Featured on <img src="http://upload.wikimedia.org/wikipedia/en/4/43/Techcrunch-transparent-photshop.gif"></a> </div> </center>
		<br>       
		<span id = "about"><a target="_blank" href="http://twitter.com/readstreamapp">About</a></span> 
		</span>
				<div id = 'twitter-button'><a href="http://twitter.com/share" class="twitter-share-button" data-url="http://ReadstreamApp.com" data-text="Enjoying reading on Readstream - a better way to consume all your Twitter links" data-count="horizontal" data-via="ReadstreamApp">Tweet</a><script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script></div>
<div id = 'fb-like'><iframe src="http://www.facebook.com/plugins/like.php?href=http%3A%2F%2FReadstreamApp.com&amp;layout=standard&amp;show_faces=false&amp;width=450&amp;action=like&amp;font&amp;colorscheme=light&amp;height=35" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:450px; height:35px;" allowTransparency="true"></iframe></div>
	
	<div class="footer"> 
		 
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