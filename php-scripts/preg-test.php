<?php

$str = "Yo Yo $@#Y'asdfadf AS DF ADSFADFASDFADSF";

$str = preg_replace('/[^a-zA-Z0-9 ]/','',$str);

echo getPermalink($str);

function getPermalink($str){
	$str = preg_replace('/[^a-zA-Z0-9 ]/','',$str);
	$str = preg_replace('/(\s)+/','-',$str);
	//$arr = explode(' ',$str);
	//$str = implode('-',$arr);
	return $str;
}
?>