<?php
include_once("connect.php");
include_once("functions.php");

$field = $_GET['field'];
$value = $_GET['value'];

if ($field == 'firstname') {
	if ($value == '') {
		echo 'You must provide your first name';
	}
}

if ($field == 'lastname') {
	if ($value == '') {
		echo 'You must provide your last name.';
	}
}

if ($field == 'email') {
	if ($value == '') {
		echo 'You must provide your email.';
	} else {
		//Check if email already exists
		$query = "SELECT * FROM users WHERE email = '".$value."'";
		$result = mysql_query($query) or die(mysql_error());
		if (mysql_num_rows($result) > 0) {
			echo 'This email has already been registered.';
		}
	}
}

if ($field == 'password') {
	if ($value == '') {
		echo 'You must create a password.';
	}
}
?>