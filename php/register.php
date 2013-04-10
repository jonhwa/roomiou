<?php
include_once("connect.php");
require("passwordhash.php");

$errors = array();
$values = array();
$hasher = new PasswordHash(8, false);

$firstname = trim(mysql_real_escape_string($_POST['firstname']));
$lastname = trim(mysql_real_escape_string($_POST['lastname']));
$email = trim(mysql_real_escape_string($_POST['email']));
$password = $_POST['password'];
$hash = $hasher->HashPassword($password);
$roomname = trim(mysql_real_escape_string($_POST['room']));
$roomid = trim(mysql_real_escape_string($_POST['roomid']));
if ($roomid != '') $roomid = intval($roomid);

$values['firstname'] = $firstname;
$values['lastname'] = $lastname;
$values['email'] = $email;
$values['password'] = $password;
$values['roomname'] = $roomname;
$values['roomid'] = $roomid;

if ($firstname == '') {
	$errors['firstname'] = 'You must provide your first name.';
}

if ($lastname == '') {
	$errors['lastname'] = 'You must provide your last name.';
}

if ($roomname == '' && $roomid == '') {
	$errors['room'] = 'You must either create or join a room.';
}

$roomExists = true;
if (is_int($roomid)) {
	$query = "SELECT id FROM rooms WHERE id='$roomid'";
	$result = mysql_query($query) or die(mysql_error());
	$roomExists = mysql_num_rows($result) > 0;
}

if ((!is_int($roomid) && $roomid != '') || !$roomExists) {
	$errors['room'] = 'This room ID is invalid.';
}

if ($email == '') {
	$errors['email'] = 'You must provide your email.';
} else {
	//Check if email already exists
	$query = "SELECT * FROM users WHERE email='$email'";
	$result = mysql_query($query) or die(mysql_error());
	if (mysql_num_rows($result) > 0) {
		$errors['email'] = 'This email has already been registered.';
	} else if (is_int($roomid)) {
		//Check if email is on the invite list
		$query = "SELECT invites FROM rooms WHERE id='$roomid'";
		$result = mysql_query($query) or die(mysql_error());
		$row = mysql_fetch_assoc($result);
		$invites = $row['invites'];
		if (strpos($invites,$email) === false) {
			$errors['email'] = 'You don\'t have an invite to this room.';
		}
	}
}

if ($password == '') {
	$errors['password'] = 'You must create a password.';
}

//Proceed if there were no errors
if (count($errors) == 0) {
	$query = "INSERT INTO users SET firstname='$firstname', lastname='$lastname', email='$email', password='$hash', notificationfreq='Daily', transactionnotifications='yes', roommatenotifications='yes', summarynotifications='yes'";
	mysql_query($query) or die(mysql_error());
	$userid = mysql_insert_id();
	
	//If room ID is blank, create a new room. Else, add the user to the room
	if ($roomid == '') {
		$query = "INSERT INTO rooms (name, roommates) VALUES ('$roomname', ',$userid')";
		mysql_query($query) or die(mysql_error());
		$roomid = mysql_insert_id();
	} else {
		//Add the userid to the room and delete the email from the invite list
		$query = "UPDATE rooms SET roommates=CONCAT(roommates,',$userid'), invites=REPLACE(invites,'$email','') WHERE id='$roomid'";
		mysql_query($query) or die(mysql_error());
	}
	
	$query = "UPDATE users SET roomid='$roomid' WHERE id='$userid'";
	mysql_query($query) or die(mysql_error());
	
	$_SESSION['user_id'] = $userid;
	$_SESSION['room_id'] = $roomid;
	header("Location: ../summary.php");
} else {
	$_SESSION['errors'] = $errors;
	$_SESSION['values'] = $values;
	header("Location: ../register.php");
}
?>