<?php 
include_once("connect.php");
include_once("functions.php");

$id = $_GET['id'];

$query = "SELECT * FROM transactions WHERE id='$id'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_assoc($result);

$type = $row['type'];
$room = $row['roomid'];
$roommates = getRoommates($room);
$total = $row['total'];
$split = getSplit($id);

if ($type == 'bill') {
	$purchaser = $row['purchaser'];
} else if ($type == 'payment') {
	$purchaser = $row['payer'];
}

for ($i=0; $i<count($roommates); $i++) {
	$user = $roommates[$i];
	$oldBalance = getBalance($user);
	$newBalance = $oldBalance;
	if ($user == $purchaser) {
		$newBalance -= $total;
	}
	$newBalance += $split[$user];

	$query = "UPDATE users SET balance='$newBalance' WHERE id='$user'";
	mysql_query($query) or die(mysql_error());
}

$query = "DELETE FROM transactions WHERE id='$id'";
mysql_query($query) or die(mysql_error());
?>