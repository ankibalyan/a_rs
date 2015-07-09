<?php

require('login/config.php');
require_once 'PHPMailer/PHPMailerAutoload.php';

//mail connection
$m = new PHPMailer;
$m->isSMTP();
$m->SMTPAuth = true;
$m->Host = 'smtp.gmail.com';
$m->Username = 'arvind.shotym@gmail.com';
$m->Password = 'arvind@123';
$m->SMTPSecure = 'ssl';
$m->Port = 465;
$m->From = 'arvind.shotym@gmail.com';
$m->FromName = 'Arvind brands';
$m->Subject = "Upload Pending";
$m->Body = 'This email is sent regarding the upload to be done that is pending.';
$m->addReplyTo('arvind.shotym@gmail.com','Reply address');

//get current weeks monday date
$current_date= date("Y-m-d");
$mdate = strtotime("$current_date monday this week");
$startdate = date('Y-m-d',$mdate);
$startdate;

//get ids of the users uploaded
$sql_uploaded = mysql_query("SELECT uploaded_user_ids FROM user_upload_track WHERE every_monday = '".$startdate."'");
$result = mysql_result($sql_uploaded,0);
$split = explode(',',$result);

//get all the user ids and check for the match with uploaded user ids
$sql_user_ids = mysql_query("SELECT id FROM users");
$result_id = array();
while($user_id = mysql_fetch_row($sql_user_ids))
{
	array_push($result_id,$user_id[0]);
}

$users_not_uploaded = array();
foreach($result_id as $userids)
{
	if(!in_array( $userids, $split))
	{
		array_push($users_not_uploaded, $userids);
	}
}

//get emails of the users who have not uploaded
$user_emails = array();
foreach ($users_not_uploaded as $id) {
	
	$sql = mysql_query("SELECT email FROM users WHERE id ='".$id."'");
	$sql_r = mysql_result($sql,0);
	array_push($user_emails,$sql_r);
}

foreach($user_emails as $email)
{
	$m->addAddress($email);
	$m->send();
}