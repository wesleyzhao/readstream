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
	    </div>
		<div id="main-box">
			<center><h1>Well this is embarrassing.</h1></center>
			   <div id="emb">
				We're three college hackers who put this site together over the course of three days, having no idea of the attention it would get. We’re firm believers in providing <b>utility</b> and <b>happiness</b> with our products, and because ReadStream isn’t currently performing at the optimal level we’d like, we’d rather scale down than compromise the experience for everyone.<br><br>
			   Thank you so much for sticking with us. Because we are committed to having only the highest quality product and we will be rolling the site back out to new users slowly over the course of the day. Please enter your email down below if you’d like to be notified when ReadStream comes back online or when release future products:
              </div>
			  <!-- Begin MailChimp Signup Form -->
			<!--[if IE]>
			<style type="text/css" media="screen">
				#mc_embed_signup fieldset {position: relative;}
				#mc_embed_signup legend {position: absolute; top: -1em; left: .2em;}
			</style>
			<![endif]--> 
			<!--[if IE 7]>
			<style type="text/css" media="screen">
				.mc-field-group {overflow:visible;}
			</style>
			<![endif]-->

			<div id="mc_embed_signup">
			<form action="http://tasteplug.us2.list-manage.com/subscribe/post?u=a76fad2df1bb28b18bd0b5143&amp;id=4aebeb0204" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" style="font: normal 100% Arial, sans-serif;font-size: 10px;">
				<fieldset style="-moz-border-radius: 4px;border-radius: 4px;-webkit-border-radius: 4px;border: 1px solid #ccc;padding-top: 1.5em;margin: .5em 0;background-color: #fff;color: #000;text-align: left;">
				<legend style="white-space: normal;text-transform: capitalize;font-weight: bold;color: #000;background: #fff;padding: .5em 1em;border: 1px solid #ccc;-moz-border-radius: 4px;border-radius: 4px;-webkit-border-radius: 4px;font-size: 1.2em;"><span>join our mailing list</span></legend>
			<div class="indicate-required" style="text-align: right;font-style: italic;overflow: hidden;color: #000;margin: 0 9% 0 0;">* indicates required</div>
			<div class="mc-field-group" style="margin: 1.3em 5%;clear: both;overflow: hidden;">
			<label for="mce-EMAIL" style="display: block;margin: .3em 0;line-height: 1em;font-weight: bold;">Email Address <strong class="note-required">*</strong>
			</label>
			<input type="text" value="" name="EMAIL" class="required email" id="mce-EMAIL" style="margin-right: 1.5em;padding: .2em .3em;width: 90%;float: left;z-index: 999;">
			</div>
					<div id="mce-responses" style="float: left;top: -1.4em;padding: 0em .5em 0em .5em;overflow: hidden;width: 90%;margin: 0 5%;clear: both;">
						<div class="response" id="mce-error-response" style="display: none;margin: 1em 0;padding: 1em .5em .5em 0;font-weight: bold;float: left;top: -1.5em;z-index: 1;width: 80%;background: FBE3E4;color: #D12F19;"></div>
						<div class="response" id="mce-success-response" style="display: none;margin: 1em 0;padding: 1em .5em .5em 0;font-weight: bold;float: left;top: -1.5em;z-index: 1;width: 80%;background: #E3FBE4;color: #529214;"></div>
					</div>
					<div><input type="submit" value="Subscribe" name="subscribe" id="mc-embedded-subscribe" class="btn" style="clear: both;width: auto;display: block;margin: 1em 0 1em 5%;"></div>
				</fieldset>	
				<a href="#" id="mc_embed_close" class="mc_embed_close" style="display: none;">Close</a>
			</form>
			</div>
			<script type="text/javascript">
			var fnames = new Array();var ftypes = new Array();fnames[0]='EMAIL';ftypes[0]='email';fnames[1]='FNAME';ftypes[1]='text';fnames[2]='LNAME';ftypes[2]='text';
			try {
			    var jqueryLoaded=jQuery;
			    jqueryLoaded=true;
			} catch(err) {
			    var jqueryLoaded=false;
			}
			var head= document.getElementsByTagName('head')[0];
			if (!jqueryLoaded) {
			    var script = document.createElement('script');
			    script.type = 'text/javascript';
			    script.src = 'http://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js';
			    head.appendChild(script);
			    if (script.readyState && script.onload!==null){
			        script.onreadystatechange= function () {
			              if (this.readyState == 'complete') mce_preload_check();
			        }    
			    }
			}
			var script = document.createElement('script');
			script.type = 'text/javascript';
			script.src = 'http://downloads.mailchimp.com/js/jquery.form-n-validate.js';
			head.appendChild(script);
			var err_style = '';
			try{
			    err_style = mc_custom_error_style;
			} catch(e){
			    err_style = 'margin: 1em 0 0 0; padding: 1em 0.5em 0.5em 0.5em; background: FFEEEE none repeat scroll 0% 0%; font-weight: bold; float: left; z-index: 1; width: 80%; -moz-background-clip: -moz-initial; -moz-background-origin: -moz-initial; -moz-background-inline-policy: -moz-initial; color: FF0000;';
			}
			var head= document.getElementsByTagName('head')[0];
			var style= document.createElement('style');
			style.type= 'text/css';
			if (style.styleSheet) {
			  style.styleSheet.cssText = '.mce_inline_error {' + err_style + '}';
			} else {
			  style.appendChild(document.createTextNode('.mce_inline_error {' + err_style + '}'));
			}
			head.appendChild(style);
			setTimeout('mce_preload_check();', 250);

			var mce_preload_checks = 0;
			function mce_preload_check(){
			    if (mce_preload_checks>40) return;
			    mce_preload_checks++;
			    try {
			        var jqueryLoaded=jQuery;
			    } catch(err) {
			        setTimeout('mce_preload_check();', 250);
			        return;
			    }
			    try {
			        var validatorLoaded=jQuery("#fake-form").validate({});
			    } catch(err) {
			        setTimeout('mce_preload_check();', 250);
			        return;
			    }
			    mce_init_form();
			}
			function mce_init_form(){
			    jQuery(document).ready( function($) {
			      var options = { errorClass: 'mce_inline_error', errorElement: 'div', onkeyup: function(){}, onfocusout:function(){}, onblur:function(){}  };
			      var mce_validator = $("#mc-embedded-subscribe-form").validate(options);
			      options = { url: 'http://tasteplug.us2.list-manage.com/subscribe/post-json?u=a76fad2df1bb28b18bd0b5143&id=4aebeb0204&c=?', type: 'GET', dataType: 'json', contentType: "application/json; charset=utf-8",
			                    beforeSubmit: function(){
			                        $('#mce_tmp_error_msg').remove();
			                        $('.datefield','#mc_embed_signup').each(
			                            function(){
			                                var txt = 'filled';
			                                var fields = new Array();
			                                var i = 0;
			                                $(':text', this).each(
			                                    function(){
			                                        fields[i] = this;
			                                        i++;
			                                    });
			                                $(':hidden', this).each(
			                                    function(){
			                                    	if ( fields[0].value=='MM' && fields[1].value=='DD' && fields[2].value=='YYYY' ){
			                                    		this.value = '';
												    } else if ( fields[0].value=='' && fields[1].value=='' && fields[2].value=='' ){
			                                    		this.value = '';
												    } else {
				                                        this.value = fields[0].value+'/'+fields[1].value+'/'+fields[2].value;
				                                    }
			                                    });
			                            });
			                        return mce_validator.form();
			                    }, 
			                    success: mce_success_cb
			                };
			      $('#mc-embedded-subscribe-form').ajaxForm(options);      

			    });
			}
			function mce_success_cb(resp){
			    $('#mce-success-response').hide();
			    $('#mce-error-response').hide();
			    if (resp.result=="success"){
			        $('#mce-'+resp.result+'-response').show();
			        $('#mce-'+resp.result+'-response').html(resp.msg);
			        $('#mc-embedded-subscribe-form').each(function(){
			            this.reset();
			    	});
			    } else {
			        var index = -1;
			        var msg;
			        try {
			            var parts = resp.msg.split(' - ',2);
			            if (parts[1]==undefined){
			                msg = resp.msg;
			            } else {
			                i = parseInt(parts[0]);
			                if (i.toString() == parts[0]){
			                    index = parts[0];
			                    msg = parts[1];
			                } else {
			                    index = -1;
			                    msg = resp.msg;
			                }
			            }
			        } catch(e){
			            index = -1;
			            msg = resp.msg;
			        }
			        try{
			            if (index== -1){
			                $('#mce-'+resp.result+'-response').show();
			                $('#mce-'+resp.result+'-response').html(msg);            
			            } else {
			                err_id = 'mce_tmp_error_msg';
			                html = '<div id="'+err_id+'" style="'+err_style+'"> '+msg+'</div>';

			                var input_id = '#mc_embed_signup';
			                var f = $(input_id);
			                if (ftypes[index]=='address'){
			                    input_id = '#mce-'+fnames[index]+'-addr1';
			                    f = $(input_id).parent().parent().get(0);
			                } else if (ftypes[index]=='date'){
			                    input_id = '#mce-'+fnames[index]+'-month';
			                    f = $(input_id).parent().parent().get(0);
			                } else {
			                    input_id = '#mce-'+fnames[index];
			                    f = $().parent(input_id).get(0);
			                }
			                if (f){
			                    $(f).append(html);
			                    $(input_id).focus();
			                } else {
			                    $('#mce-'+resp.result+'-response').show();
			                    $('#mce-'+resp.result+'-response').html(msg);
			                }
			            }
			        } catch(e){
			            $('#mce-'+resp.result+'-response').show();
			            $('#mce-'+resp.result+'-response').html(msg);
			        }
			    }
			}

			</script>
			<!--End mc_embed_signup-->
			
		</div>
				<br><br>
		<br>       
		<span id = "about"><a href="http://twitter.com/readstreamapp">About</a></span> 
		</span>
				<div id = 'twitter-button'><a href="http://twitter.com/share" class="twitter-share-button" data-url="http://ReadstreamApp.com" data-text="Enjoying reading on Readstream - a better way to consume all your Twitter links" data-count="horizontal" data-via="ReadstreamApp">Tweet</a><script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script></div>
<div id = 'fb-like'><iframe src="http://www.facebook.com/plugins/like.php?href=http%3A%2F%2FReadstreamApp.com&amp;layout=standard&amp;show_faces=false&amp;width=450&amp;action=like&amp;font&amp;colorscheme=light&amp;height=35" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:450px; height:35px;" allowTransparency="true"></iframe></div>
	
	<div class="footer"> 
		 
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