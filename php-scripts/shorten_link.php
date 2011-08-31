<?php
$url = $_GET['url'];
$ch = curl_init();  
curl_setopt($ch, CURLOPT_URL, "$url");  
curl_setopt($ch, CURLOPT_HEADER, 1);  
curl_setopt($ch, CURLOPT_NOBODY, 1);  
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
curl_setopt($ch, CURLOPT_TIMEOUT, 10);  
  
$result = curl_exec($ch);   
  
if (preg_match("/Location\:/","$result")) {  
    $url = explode("Location: ",$result);  
    $reversed_url = explode("\r",$url[1]);  
    echo $reversed_url[0];  
} else {  
    print_r($result);  
}  
?>