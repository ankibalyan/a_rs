<?php

require('login/config.php');

$current_date= date("Y-m-d");
$mdate = strtotime("$current_date monday this week");
$startdate = date('Y-m-d',$mdate);
$startdate;

$sql = mysql_query("SELECT count(*) FROM user_upload_track WHERE every_monday='".$startdate."'");
$sql_r = mysql_result($sql,0);
 if($sql_r==0)
 {
 	$sql = mysql_query("INSERT INTO user_upload_track(`every_monday`,`uploaded_user_ids`) VALUES('".$startdate."','".$user_id."')");
 	echo "inserted";
 }else{
 	$sql = mysql_query("UPDATE user_upload_track SET uploaded_user_ids= CONCAT(uploaded_user_ids,',1') WHERE every_monday='".$startdate."'");
 	echo "updated";
 }
