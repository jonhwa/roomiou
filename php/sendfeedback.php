<?php
session_start();
include_once("connect.php");
include_once("functions.php");

$feedback = $_POST['feedbacktxt'];

$to = 'connieyuan92@gmail.com';
$subject = 'Someone submitted feedback for RoomIOU';

	$mail_body="Feedback: ";
	$mail_body.="$feedback";
	$_SESSION['message']='';
	$headers = "From: RoomIOU <roomiou@roomiou.com>\r\n";
	$headers .= "Reply-To: RoomIOU <support@roomiou.com>\r\n";
	$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
	$sent = mail($to, $subject, $mail_body, $headers);

	if ($sent) {
		$_SESSION['message']='Thank you for sending us feedback!';
		header("Location: ../feedback.php");
	} else {
		$_SESSION['message']='Sorry! An error occurred. Try sending feedback again.';
		header("Location: ../feedback.php");
	}
?>