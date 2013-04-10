<?
require("connect.php");
require("passwordhash.php");
$user = $_SESSION['user_id'];

$errors = array();
$values = array();
$hasher = new PasswordHash(8, false);

$oldpass = $_POST['oldpass'];
$newpass = $_POST['newpass'];
$confirm = $_POST['confirm'];

$values['oldpass'] = $oldpass;
$values['newpass'] = $newpass;
$values['confirm'] = $confirm;

if ($newpass != $confirm) {
	$errors['confirm'] = 'Your passwords do not match';
}

if ($newpass == '') {
	$errors['newpass'] = 'You have to create a new password';
}

$query = "SELECT password FROM users WHERE id='$user'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_assoc($result);
$password = $row['password'];
if (!$hasher->CheckPassword($oldpass, $password)) {
	$errors['oldpass'] = 'Your current password is incorrect';
}

//Proceed if there were no errors
if (count($errors) == 0) {
	$hash = $hasher->HashPassword($newpass);
	$query = "UPDATE users SET password='$hash' WHERE id='$user'";
	mysql_query($query) or die(mysql_error());
	
	$_SESSION['success'] = 'Your password has been changed';
	header("Location: ../settings.php");
} else {
	$_SESSION['errors'] = $errors;
	$_SESSION['values'] = $values;
	header("Location: ../settings.php?go=pass");
}
?>