<?php 
include_once("connect.php");
include_once("functions.php");

$type = $_GET['type'];

if ($type == 'roommate') {
	getPayment($_GET['user1'], $_GET['user2']);
} else if ($type == 'balance') {
	$user = $_GET['user'];
	$room = $_GET['room'];
	$roommates = getRoommates($room);
	for ($i=0; $i<count($roommates); $i++) {
		if ($user != $roommates[$i]) {
			getPayment($user, $roommates[$i]);
		}
	}
}
echo '<span id="explanationClick" onclick="displayExplanation();">How is this calculated?</span><span id="explanation"></span>';

function getPayment($user1, $user2) {
	$user1balance = 0;
	
	if ($user1 != $user2 && $user2 != 0) {
		//Take all bills into account
		$query = "SELECT id, purchaser FROM transactions WHERE purchaser='$user1' OR purchaser='$user2'";
		$result = mysql_query($query) or die(mysql_error());
		while ($row = mysql_fetch_assoc($result)) {
			$purchaser = $row['purchaser'];
			$split = getSplit($row['id']);
			
			if ($purchaser == $user1) {
				$user1balance += $split[$user2];
			} else if ($purchaser == $user2) {
				$user1balance -= $split[$user1];
			}
		}
		
		//Take all payments into account
		$query = "SELECT id, payer FROM transactions WHERE payer='$user1' OR payer='$user2'";
		$result = mysql_query($query) or die(mysql_error());
		while ($row = mysql_fetch_assoc($result)) {
			$payer = $row['payer'];
			$split = getSplit($row['id']);
			
			if ($payer == $user1) {
				$user1balance += $split[$user2];
			} else if ($payer == $user2) {
				$user1balance -= $split[$user1];
			}
		}
		
		$user1name = getName($user1);
		$user2name = getName($user2);
		
		echo '<p>';
		if ($user1balance < 0) {
			echo '</br><span style="color: #898989;">';
			echo $user1name.' owes '.$user2name.'<span style="color: #e6d61b;"> $'.number_format(abs($user1balance),2);
			echo '</span></span>';
		} else if ($user1balance > 0) {
			echo '</br><span style="color: #898989;">';
			echo $user2name.' owes '.$user1name.'<span style="color: #e6d61b;"> $'.number_format($user1balance,2);
			echo '</span></span>';
		} else {
			echo '</br><span style="color: #898989;">'.$user1name.' and '.$user2name.' are even</span>';
		}
		echo '</p>';
	}
	
	return $user1balance;
}
?>