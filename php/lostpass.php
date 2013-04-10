<?
include_once("connect.php");
require("passwordhash.php");

$errors = array();
$values = array();
$hasher = new PasswordHash(8, false);

$email = mysql_real_escape_string($_POST['email']);

$values['email'] = $email;

$query = "SELECT firstname, id FROM users WHERE email='".$email."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);

if (empty($row['id'])) {
	$errors['email'] = "This account doesn't exist.";
}

//Proceed if there were no errors
if (count($errors) == 0) {
	$id = $row['id'];
	$firstname = $row['firstname'];
	
	$set = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
	$length = mt_rand(8,10);
	$newPassword = generateString($set, $length);
	
	//Store new password in database
	$newPasswordHash = $hasher->HashPassword($newPassword);
	$query = "UPDATE users SET password='$newPasswordHash' WHERE id='$id'";
	mysql_query($query) or die(mysql_error());
	
	//Email new password to user
	$to = $email;
	$subject = "Reset Password";
	$mail_body = '<html>
	<body>
	<div style="font-family:Arial,Helvetica,sans-serif; font-size:14px; color:#696762 !important">
	<div style="background-color: #2c2c2c;">
	<img src="http://roomiou.com/img/Roomioularge.png" height="55" display:block/>
	</div>
	<div style="background:url(http://roomiou.com/css/cssimg/darkbluebkg.png); background-color:#b0dfed; padding: 40px">
	<div style="background: #FFFFFF; max-width:400px; margin:0 auto; border:1px solid #CDCDCD">	
	<div style="padding: 40px;">

	<p>Dear '.$firstname.',</p>
	<p>You recently asked for your password to be reset.</p>
	<p>Your temporary password is: '.$newPassword.'</p>
	<p>Please log in and change your password at <a href="http://roomiou.com/index.php?go=pass">this link</a>.</p>
	<p>If you didn\'t ask for a password reset, please email us at <strong>support@roomiou.com</strong>.</p>
	<p>Thank you,</p>
	<p style="display:inline;">The RoomIOU Team</br>RoomIOU | www.roomiou.com</p>
	</div>
	</div>
	</div>
	</div>
	</body>
	</html>';
	
	$headers = "From: RoomIOU <roomiou@roomiou.com>\r\n";
	$headers .= "Reply-To: RoomIOU <support@roomiou.com>\r\n";
	$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
	$sent = mail($to, $subject, $mail_body, $headers);
	$errors['email'] = "A new password has been sent to your email address";
	$_SESSION['errors'] = $errors;
	header("Location: ../index.php");
} else {
	$_SESSION['errors'] = $errors;
	$_SESSION['values'] = $values;
	header("Location: ../lostpass.php");
}

function generateString($set, $length) {
	$string = '';
	$numChars = strlen($set);
	for ($i = 0; $i < $length; $i++) {
		$j = mt_rand(1, $numChars);
		$char = $set[$j-1];
		$string .= $char;
	}
	return $string;
}
?>