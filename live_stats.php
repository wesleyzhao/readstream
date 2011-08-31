<?php
require_once("php-scripts/mysql_connect.php");

mysqlConnect();
$res_users = mysql_query("SELECT id FROM users");
$res_links = mysql_query("SELECT id FROM links");
$res_views = mysql_query("SELECT id from views");

$num_users = mysql_num_rows($res_users);
$num_links = mysql_num_rows($res_links);
$num_views = mysql_num_rows($res_views);

?>
<html>
<head>
	<title>Readstream Live Statistics</title>
</head>

<body>
<div class = 'live-stat'>Users: <?=$num_users?></div>
<div class = 'live-stat'>Links: <?=$num_links?></div>
<div class = 'live-stat'>Views: <?=$num_views?></div>
</body>