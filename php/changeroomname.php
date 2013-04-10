<?
include_once("connect.php");
$room = $_SESSION['room_id'];

$errors = array();

$name = $_POST['name'];

if ($name == '') {
	$errors['name'] = 'Your room must have a name';
}

//Proceed if there were no errors
if (count($errors) == 0) {
	$query = "UPDATE rooms SET name='$name' WHERE id='$room'";
	mysql_query($query) or die(mysql_error());
	
	$_SESSION['success'] = 'Your room name has been updated';
	header("Location: ../settings.php");
} else {
	$_SESSION['errors'] = $errors;
	header("Location: ../settings.php?go=room");
}
?>