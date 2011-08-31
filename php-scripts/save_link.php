<?php
require_once('mysql_connect.php');
session_start();
$link_id = $_GET['link_id'];

$user_id = $_SESSION['user_id'];
if ($user_id){
$res = mysql_query("SELECT id FROM views WHERE link_id='$link_id' AND twitter_id='$user_id'");
if (mysql_num_rows($res)){
	$row = mysql_fetch_array($res);
	mysql_query("UPDATE views SET is_saved='1' WHERE id='{$row['id']}'");
}
else echo 'not found';
}
else echo 'stop trying to hack...';
?>