<?php 
$hostname = "arvindecom.c268nehlpjb4.ap-southeast-1.rds.amazonaws.com";
$username = "arvindadmin";
$password = "Passarvind";
$database = "aravind";
$conn = mysql_connect($hostname, $username, $password, $database) or die('Error in connecting database');
mysql_select_db($database);
?>