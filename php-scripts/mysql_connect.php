<?php
//to be included/required so that a function 'mysqlConnect()' provides a connection to the database
function mysqlConnect(){
	//connects to the local MySQL favoritething database
	$con = mysql_connect('localhost','username','password');
	mysql_select_db('username',$con);
}                                                                                          
?>
