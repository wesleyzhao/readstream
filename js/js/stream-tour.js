var buffer_one = "";
var buffer_two = ""; 

var buffer_array = new Array();
var position = 0;  

var LOADING_NEW_ARTICLES = 1;
var NOT_LOADING = 0;        

var has_loaded = 0;
var state = 0;     

if (!Array.indexOf) {
  Array.prototype.indexOf = function (obj, start) {
    for (var i = (start || 0); i < this.length; i++) {
      if (this[i] == obj) {
        return i;
      }
    }
    return -1;
  }
}

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
	$(".read-more").html("");
} 

function loadComments(){
	$("#comments").html("Loading Comments....");
	var link = document.getElementById("full-article-link").href; 
	$("#comments").load("php-scripts/get_comments.php?term=" + link);
}                 

function makeReply(handle, tweet_id){   
	$("#tweet").focus();  
	var link = document.getElementById("full-article-link").href;               
	$(".tweet").html(" @"+handle + " " + link + " via @readstreamapp"); 
	$(".reply-hide").html(tweet_id);  
	chars = $('.tweet').val().length;
	chars = 140-chars;
	$('#chars-count').html(chars + " characters left.");
    $(".tweet").selectRange(0,0);

	
	window.scrollTo(0,400);  
}  

function addArticle(data){
	buffer_array.push(data);
	
	//first we have to find the title
	var start = data.indexOf("<h1>")+4;
	var end = data.indexOf("</h1>");
	var title = data.substring(start, end);
	var title_short = title.substring(0, 30);
	title = title_short + "..."; 
	var article_position = buffer_array.length - 1;
	$("#timeline").append("<div class='timeline-item' id='article-"+ article_position +"'><a href='javascript:changePosition("+article_position+")'>"+title+"</a></div>");
	
} 

function changePosition(new_position){
	if(new_position < buffer_array.length && new_position > -1){ 
		$("#article-"+new_position).toggleClass("highlight"); 
		$("#article-"+position).toggleClass("highlight"); 
		position = new_position;
		document.getElementById("replace").innerHTML = buffer_array[position];
		loadComments();
		if((position > buffer_array.length - 7) && (state == NOT_LOADING)){
			var data = buffer_array[buffer_array.length-1];
			var start = data.indexOf("<span id=\"last-tweet\"")+44;   
			var last = data.substring(start, data.length-7);
			addBuffer(last);
		}       
	}
}

function addBuffer(last_tweet){
	state = LOADING_NEW_ARTICLES;
	$(".loading-hidden").toggleClass("loading");
	$.get("php-scripts/get_user_feed_tour.php?max_id="+last_tweet, function(data){
		state = NOT_LOADING;
		$(".loading-hidden").toggleClass("loading"); 
		if(buffer_array.indexOf(data) == -1){
			addArticle(data);
			var start = data.indexOf("<span id=\"last-tweet\"")+44;   
			var last = data.substring(start, data.length-7);
			if(!has_loaded && buffer_array.length == 2){
				document.getElementById("replace").innerHTML = buffer_array[0];
				loadComments(); 
				$(".hideme").toggle();
				$("#article-0").toggleClass("highlight"); 
				
			}
			if(position > buffer_array.length - 7){
				var data = buffer_array[buffer_array.length-1];
				var start = data.indexOf("<span id=\"last-tweet\"")+44;   
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
	var link = document.getElementById("short-url").innerHTML;
	var name = document.getElementById("current-twitter-username").innerHTML;
	var title = $("h1").html();
	$(".tweet").html(title + " " + link + " via @readstreamapp");   
	chars = $('.tweet').val().length;
	chars = 140-chars;
	$('#chars-count').html(chars + " characters left.");
    $(".tweet").selectRange(0,0);
}
                                    

$(document).keydown(function(e){
    if (e.keyCode == 37 && position > 0) {
		window.scrollTo(0,0);
		$('#chars-count').html("140 characters left.");
		$(".tweet").val("Leave a Tweet");   
	    var replace = buffer_array[position]; 
		if(replace != "" && replace){        
			changePosition(position -1);
			loadComments();       
		}   
    }  
	else if (e.keyCode == 39 || e.which == 39){
		window.scrollTo(0,0);
		$('#chars-count').html("140 characters left.");
		$(".tweet").val("Leave a Tweet");
		if(position < buffer_array.length-1){    
			var replace = buffer_array[position];
			if(replace != "" && replace){    
				changePosition(position + 1);
				loadComments();   
				if((position > buffer_array.length - 7) && (state == NOT_LOADING)){
					var data = buffer_array[buffer_array.length-1];
					var start = data.indexOf("<span id=\"last-tweet\"")+44;   
					var last = data.substring(start, data.length-7);
					addBuffer(last);
				}
			} 
		}
	}
});
        