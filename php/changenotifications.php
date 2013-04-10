<?
include_once("connect.php");
$user = $_SESSION['user_id'];

$frequency = $_POST['frequency'];
$transactions = $_POST['transactions'];
$roommates = $_POST['roommates'];
$summary = $_POST['summary'];

if ($transactions == 'no' && $roommates == 'no' && $summary == 'no') {
	$frequency = 'Never';
}

$query = "UPDATE users SET notificationfreq='$frequency', transactionnotifications='$transactions', roommatenotifications='$roommates', summarynotifications='$summary' WHERE id='$user'";
mysql_query($query) or die(mysql_error());

$_SESSION['success'] = 'Your notifications schedule has been updated';
header("Location: ../settings.php");
?>