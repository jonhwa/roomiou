<?php
include_once("connect.php");
include_once("functions.php");

$dayofweek = date('w'); //0 to 6 Sunday to Saturday
$dayofmonth = date('j'); //1 to 31

$query = "SELECT * FROM users";
$result = mysql_query($query) or die(mysql_error());
while ($row = mysql_fetch_assoc($result)) {
	$id = $row['id'];
	$email = $row['email'];
	$roomid = $row['roomid'];
	$notificationfreq = $row['notificationfreq'];

	//Send the notification if the user wants it daily, weekly and a Monday, or monthly and the first of the month
	if (($notificationfreq == "Monthly" && $dayofmonth == "1") || ($notificationfreq == "Weekly" && $dayofweek == "1") || ($notificationfreq == "Daily")) {
		$to = $email;
		$subject = $notificationfreq." RoomIOU Summary for ".getName($id);
		$mail_body = '<html>
		<body>
		<div style="font-family:Arial,Helvetica,sans-serif; font-size:14px; color:#696762 !important">
		<div style="background-color: #2c2c2c;">
		<img src="http://roomiou.com/img/Roomioularge.png" height="55" display:block/>
		</div>
		<div style="background:url(http://roomiou.com/css/cssimg/darkbluebkg.png); background-color:#b0dfed; padding: 40px">
		<div style="background: #FFFFFF; max-width:400px; margin:0 auto; border:1px solid #CDCDCD">
		<div style="padding: 40px;">
		<p>'.$row['firstname'].' '.$row['lastname'].', here is your RoomIOU summary:</p>';
		
		//Determine how far back to go when pulling new information
		if ($notificationfreq == "Daily") $range = 60 * 60 * 24;
		if ($notificationfreq == "Weekly") $range = 60 * 60 * 24 * 7;
		if ($notificationfreq == "Monthly") $range = 60 * 60 * 24 * 31;
		
		//Determine which notifications to send
		$transactionnotifications = $row['transactionnotifications'];
		$roommatenotifications = $row['roommatenotifications'];
		$summarynotifications = $row['summarynotifications'];
		
		if ($transactionnotifications == 'yes') {
			$query2 = "SELECT * FROM transactions WHERE roomid='$roomid' ORDER BY date DESC";
			$result2 = mysql_query($query2) or die(mysql_error());
			
			//Initialize HTML string
			$html = '<h3 style="text-decoration: underline;">Transaction Summary</h3>';
			
			//If there are no new transactions, display placeholder text
			if (mysql_num_rows($result2) == 0) {
				$html .= '<p>There are no new transactions to report.</p>';
			} else {
				$row2 = mysql_fetch_assoc($result2);
				$html .= '<ul style="list-style-type: none; padding: 0; margin-left: 0;">';

				//Send all transactions that are within the defined date range
				while (time() - $row2['date'] < $range) {
					$type = $row2['type'];
					$total = number_format($row2['total'], 2);
					$timestamp = $row2['date'];
					$diff = time() - $timestamp;
					if ($diff < 24 * 60 * 60) {
						$when = 'today';
					} else if ($diff < 24 * 60 * 60 * 2) {
						$when = 'yesterday';
					} else {
						$numDays = ceil($diff / (24 * 60 * 60));
						$when = $numDays.' days ago';
					}
					
					//Continue building HTML for the email
					if ($type == 'bill') {
						$item = strtolower($row2['item']);
						$purchaser = getName($row2['purchaser']);
						$html .= '<li>'.$purchaser.' paid $'.$total.' for '.$item.' '.$when.'.</li>';
					} else if ($type == 'payment') {
						$payer = getName($row2['payer']);
						$recipient = getName($row2['recipient']);
						$html .= '<li>'.$payer.' paid '.$recipient.' $'.$total.' '.$when.'.</li>';
					}
					$row2 = mysql_fetch_assoc($result2);
				}
				$html .= '</ul>';
			}
			$mail_body .= $html;
		}
		
		if ($roommatenotifications == 'yes') {
			$lastRoommates = explode(',',$row['lastroommates']);
			$roommates = getRoommates($roomid);
			$newRoommates = array_diff($roommates, $lastRoommates);
			$oldRoommates = array_diff($lastRoommates, $roommates);
			
			//Build HTML for the email
			$html = '<h3 style="text-decoration: underline;">Roommate Activity</h3><ul style="list-style-type: none; padding: 0; margin-left: 0;">';
			
			//If there was no roommate activity, display placeholder text
			if (count($newRoommates) == 0 && count($oldRoommates) == 0) {
				$html .= '<p>There are no new transactions to report.</p>';
			} else {
				for ($i = 0; $i < count($newRoomates); $i++) {
					$name = getName($newRoommates[$i]);
					$html .= '<li>'.$name.' joined your room.</li>';
				}
				
				for ($i = 0; $i < count($oldRoommates); $i++) {
					$name = getName($oldRoommates[$i]);
					$html .= '<li>'.$name.' left your room.</li>';
				}
				
				//Build roommate string to store into users
				$string = '';
				for ($i = 0; $i < count($roommates); $i++) {
					$string .= $roommates[$i];
					if ($i + 1 < count($roommates)) {
						$string .= ',';
					}
				}
				mysql_query("UPDATE users SET lastroommates='$string' WHERE id='$id'") or die(mysql_error());
			}
			
			$mail_body .= $html;
		}
		
		if ($summarynotifications == 'yes') {
			$balance = $row['balance'];
			$lastbalance = $row['lastbalance'];
			$difference = number_format($balance - $lastbalance, 2);
			
			//Build HTML string for the email
			$html = '<h3 style="text-decoration: underline;">Balance Summary</h3>';
			if ($balance < 0) {
				$html .= '<p>You currently owe <span style="color: #CC0000;">$'.abs($balance).'</span> to your roommates.';
			} else if ($balance > 0) {
				$html .= '<p>You currently are owed <span style="color: #009900;">$'.abs($balance).'</span> by your roommates.';
			} else {
				$html .= '<p>You currently have a balance of $'.abs($balance).'.';
			}
			$html .= ' This is a difference of $'.$difference.' since the last time we emailed you.</p>';
			$mail_body .= $html;
			
			mysql_query("UPDATE users SET lastbalance=balance WHERE id='$id'") or die(mysql_error());
		}
		
		//Finish and send the email
		$mail_body .= '<p style="display:inline;">The RoomIOU Team</br>RoomIOU | www.roomiou.com</p>
		</div>
		</div>
		</div>
		</div>
		</body>
		</html>';
		
		echo $mail_body;
		exit;
		
		$headers = "From: RoomIOU <roomiou@roomiou.com>\r\n";
		$headers .= "Reply-To: RoomIOU <support@roomiou.com>\r\n";
		$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
		mail($to, $subject, $mail_body, $headers);
	}
}
?>