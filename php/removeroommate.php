<?
include_once("connect.php");
include_once("functions.php");

$room = $_SESSION['room_id'];
$user = $_GET['user'];
$name = getName($user);
$balance = $_GET['balance'];

$query = "UPDATE users SET roomid='0' WHERE id='$user'";
mysql_query($query) or die(mysql_error());

$query = "UPDATE rooms SET roommates=REPLACE(',$user','') WHERE id='$room'";
mysql_query($query) or die(mysql_error());

//If there is a remaining balance, divide it equally between the remaining roommates
if ($balance != 0) {
	$roommates = getRoommates($room); //Only get the remaining roommates
	$numRoommates = count($roommates);
	$amount = $balance / $numRoommates;
	$amount = round($amount, 2);
	
	//Adjust each roommates balance
	for ($i = 0; $i < $numRoommates; $i++) {
		$roommate = $roommates[$i];
		$oldBalance = getBalance($roommate);
		
		//If this is the last roommate, only add the remaining balance (adjusts for rounding errors)
		if ($i + 1 == $numRoommates) {
			$newBalance = $oldBalance + ($balance - $amount * 3);
		} else {
			$newBalance = $oldBalance + $amount;
		}
		
		$query = "UPDATE users SET balance='$newBalance' WHERE id='$roommate'";
		mysql_query($query) or die(mysql_error());
	}
}

echo $name.' was successfully removed from your room';
?>