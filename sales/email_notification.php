<?php 
	require 'login/config.php';
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
	$m->Subject = "Upload Stocks";
	$m->Body = 'This email is sent to notify for the stock uploads every monday';
	$m->addReplyTo('reply@gmail.com','Reply address');

	$sql = "SELECT email FROM users WHERE user_role = 'd'";
	$result = mysql_query($sql);
	
	$emails = array();
	while($e = mysql_fetch_row($result)) {

		array_push($emails,$e[0]);

	}
	foreach ($emails as $email) {
		$m->addAddress($email);
		$m->send();
	}
 $n = new Datetime
 ?>
