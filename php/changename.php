<?
include_once("connect.php");
$user = $_SESSION['user_id'];

$errors = array();
$values = array();

$firstname = $_POST['firstname'];
$lastname = $_POST['lastname'];
$email = $_POST['email'];
$password = $_POST['password'];
$check = $_POST['check'];

$values['firstname'] = $firstname;
$values['lastname'] = $lastname;
$values['email'] = $email;
$values['password'] = $password;

$password = md5($password);

if ($firstname == '') {
	$errors['firstname'] = 'You have to have a first name';
}

if ($lastname == '') {
	$errors['lastname'] = 'You have to have a last name';
}

if ($email == '') {
	$errors['email'] = 'You have to have an email';
}

if ($password != $check) {
	$errors['password'] = 'Your password is incorrect';
}

//Proceed if there were no errors
if (count($errors) == 0) {
	$query = "UPDATE users SET firstname='$firstname', lastname='$lastname', email='$email' WHERE id='$user'";
	mysql_query($query) or die(mysql_error());
	
	$_SESSION['success'] = 'Your name and email have been updated';
	header("Location: ../settings.php");
} else {
	$_SESSION['errors'] = $errors;
	$_SESSION['values'] = $values;
	header("Location: ../settings.php?go=name");
}
?>