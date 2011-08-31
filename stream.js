var buffer_one = "";
var buffer_two = ""; 

var buffer_array = new Array();
var position = 0;  

var LOADING_NEW_ARTICLES = 1;
var NOT_LOADING = 0;        

var has_loaded = 0;
var state = 0;   

$.fn.selectRange = function(start, end) {
    return this.each(function() {
        if (this.setSelectionRange) {
            this.focus();
            this.setSelectionRange(start, end);
        } else if (this.createTextRange) {
            var range = this.createTextRange();
            range.collapse(true);
            range.moveEnd('character', end);
            range.moveStart('character', start);
            range.select();
        }
    });
};

function readMore(){
	var item = document.getElementById("hidden");
	if (item) {                
				item.className=(item.className=='hidden')?'unhidden':'hidden';
	 }
	$(".read-more").html("<a href='javascript:readMore()'>Click here to read less.</a>");
} 

function loadComments(){
	$("#comments").html("Loading Comments....");
	var link = document.getElementById("full-article-link").href; 
	$("#comments").load("php-scripts/get_comments.php?term=" + link);
}                 

function makeReply(handle, tweet_id){   
	$("#tweet").focus();  
	var link = document.getElementById("full-article-link").href;               
	$(".tweet").html("@"+handle + " " + link + " via @readstreamapp"); 
	$(".reply-hide").html(tweet_id);  
	chars = $('.tweet').val().length;
	chars = 140-chars;
	$('#chars-count').html(chars + " characters left.");
    $(".tweet").selectRange(0,0);   
	window.scrollTo(0,400);  
}

function addBuffer(last_tweet){
	state = LOADING_NEW_ARTICLES;  
	
	$.get("php-scripts/get_user_feed.php?max_id="+last_tweet, function(data){
		state = NOT_LOADING;
		if(buffer_array.indexOf(data) == -1){
			buffer_array.push(data);
			var start = data.indexOf("<span id=\"last-tweet\"")+50;   
			var last = data.substring(start, data.length-7);
			if(!has_loaded && buffer_array.length == 2){
				document.getElementById("replace").innerHTML = buffer_array[0];
				loadComments(); 
				$(".hideme").toggle(); 
				
			}
			if(position > buffer_array.length - 7){
				var data = buffer_array[buffer_array.length-1];
				var start = data.indexOf("<span id=\"last-tweet\"")+50;   
				var last = data.substring(start, data.length-7);
				addBuffer(last);
			}
			
		}
	});
}

function retweet(tweet_id, retweet){
	if(retweet){ 
		$(".retweet").html("<span class='message'>Retweeted.</a>");
	   	         
	}else{
		$("a#retweet-"+tweet_id).html("<span class='message'>Retweeted.</a>");
	}  
	 $.get("php-scripts/send_retweet.php?tweet_id="+tweet_id, function(data){
		
		});
}

function unfollow(friend_id){ 
	$(".unfollow").html("<span class='message'>Unfollowed.</a>");
	$.get("php-scripts/destroy_friendship.php?friend_id="+friend_id, function(data){
		
	});
}

function sendTweet(){
	var tweet_id =  $("#last-tweet").html();  
	var reply = $(".reply-hide").html();
	if(reply != ""){
		tweet_id = reply;            
		$(".reply-hide").html("");
	}                    
	var text = $(".tweet").val(); 
	$(".tweet").val("Tweet sent!");
	$.get("php-scripts/send_tweet.php?tweet_text="+text+"&tweet_id="+tweet_id, function(data){
		/*	Change unfollow to unclickable, change color, and w/e else */ 
		loadComments();
	});  
	
}
$(document).ready(function(){
	addBuffer(0);  
	
	$('#tweet').keyup(function(){ 
		chars = $('.tweet').val().length;
		chars = 140-chars;
		$('#chars-count').html(chars + " characters left.");
	});
	
});  

function twitReplace(){
	var link = document.getElementById("full-article-link").href;
	var name = document.getElementById("current-twitter-username").innerHTML;
	$(".tweet").html(link + " via @" + name + " @readstreamapp");   
	chars = $('.tweet').val().length;
	chars = 140-chars;
	$('#chars-count').html(chars + " characters left.");
    $(".tweet").selectRange(0,0);
}
                                    

$(document).keydown(function(e){
    if (e.keyCode == 37 && position > 0) {
		window.scrollTo(0,0);
		$('#chars-count').html("140 characters left.");
		$(".tweet").val("Leave a Twitter comment"); 
        position -= 1;
	    var replace = buffer_array[position]; 
		if(replace != "" && replace){        
			document.getElementById("replace").innerHTML = replace; 
			loadComments();       
		}   
    }  
	else if (e.keyCode == 39 || e.which == 39){
		window.scrollTo(0,0);
		$('#chars-count').html("140 characters left.");
		$(".tweet").val("Leave a Twitter comment");
		if(position < buffer_array.length-1){
			position += 1;   
			var replace = buffer_array[position];
			if(replace != "" && replace){    
				document.getElementById("replace").innerHTML = replace;
				loadComments();   
				if((position > buffer_array.length - 7) && (state == NOT_LOADING)){
					var data = buffer_array[buffer_array.length-1];
					var start = data.indexOf("<span id=\"last-tweet\"")+50;   
					var last = data.substring(start, data.length-7);
					addBuffer(last);
				}
			} 
		}
	}
});

/*$(document).ready(function(){
	$("#replace").load("../php-scripts/get_user_feed.php", function(){
		var last_tweet = $("#last-tweet").html();
		$.get("../php-scripts/get_user_feed.php?max_id="+last_tweet, function(data){
			buffer_one = data;  
		});
	});
	      
	
});                           
    
$(document).keydown(function(e){
    if (e.keyCode == 37) {         
    }  
	else if (e.keyCode == 39){ 
		var test = ""; 
		var test = $("#last-tweet").html();  
		var last_tweet = "";
		if (test.length > 1){
			last_tweet = test;
		}
		if(buffer_one == ""){
			$("#replace").load("../php-scripts/get_user_feed.php?max_id="+last_tweet, function(){
				var last_tweet = $("#last-tweet").html();
				$.get("../php-scripts/get_user_feed.php?max_id="+last_tweet, function(data){
					buffer_one = data;  
				});
			});
		}else{
			$("#replace").html(buffer_one); 
			buffer_one = "";
			$.get("../php-scripts/get_user_feed.php?max_id="+last_tweet, function(data){
				buffer_one = data;
			});
		}   
		  
	}
});*/

