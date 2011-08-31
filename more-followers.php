<?php      
session_start(); 
$_SESSION['more-followers'] = 1;
if (!($_SESSION['ot'] && $_SESSION['ots'])) header ("Location: http://readstreamapp.com/");
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
<script type="text/javascript">	
	
    function follow(username){
	$("#"+username+"-twitter").html("<span class='message'>Following</a>");
	$.get("php-scripts/create_friendship.php?friend_screen_name="+username, function(data){

	});
	}

</script> 
<link rel="icon" href="favicon.ico" type="image/x-icon"> 
<link rel="shortcut icon" href="favicon.ico" type="image/x-icon"> 
<script type="text/javascript">var _sf_startpt=(new Date()).getTime()</script>
</head>                                         
<body>
	
	<div id="content-index">
		<div id="top">
			<div id="right-bar">an intoxicating way to read what's happening now </div>
			<div id="logo"><a href="/"><img src="images/logo.png"/ alt="Readstream"></a></div> 
	    </div>
		<div id="main-box">
			<center><h1>We can improve your experience</h1></center>
			<h4>Our software has detected that you don't follow very many people on Twitter. We recommend that you follow a few more relevant Twitter accounts before continuing. (Of course this is optional.)</h4><center>                                                        
			<table class="twitter-table">
				<tr><td>@nytimes<br><img class="twimage" src="images/ny-times-logo.jpg"><div id="nytimes-twitter"><a href="javascript:follow('nytimes');">Follow</a></div></td>
					<td>@techcrunch<br><img class="twimage" src="images/techcrunch-logo.png"><div id="techcrunch-twitter"><a href="javascript:follow('techcrunch');">Follow</a></div></td>
						<td>@ycombinatornews<br><img class="twimage" src="images/ycnews-logo.gif"><div id="ycombinatornews-twitter"><a href="javascript:follow('ycombinatornews');">Follow</a></div></td>
							<td>@espn<br><img class="twimage" src="images/espn-logo.gif"><div id="espn-twitter"><a href="javascript:follow('espn');">Follow</a></div></td></tr><tr>
								<td>@mashable<br><img class="twimage" src="images/mashable-logo.gif"><div id="mashable-twitter"><a href="javascript:follow('mashable');">Follow</a></div></td>
					<td>@wsj<br><img class="twimage" src="images/wsj-logo.jpg"><div id="wsj-twitter"><a href="javascript:follow('wsj');">Follow</a></div></td>
					<td>@peoplemag<br><img class="twimage" src="images/people-logo.jpg"><div id="peoplemag-twitter"><a href="javascript:follow('peoplemag');">Follow</a></div></td>
					<td>@theonion<br><img class="twimage" src="images/the-onion-logo.png"><div id="theonion-twitter"><a href="javascript:follow('theonion');">Follow</a></div></td></tr>       
			</table>
			<br> 
			<a href="/stream.php"><img src="images/nextbutton.png"></a>
			</center>                                                     
		</div>
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