<?php
include_once("connect.php");
include_once("functions.php");

$roomid = $_GET['roomid'];
$userid = $_GET['userid'];
$invitename = $_GET['name'];
$email = $_GET['email'];

//$emails = $_GET['emails'];
$name = getName($userid);

//$arr = preg_split("/[, \n] /", $emails);
//for ($i = 0; $i < count($arr); $i++) {
	//$email = trim($arr[$i]);
	
	$link = "http://www.roomiou.com/register.php?room=".$roomid."&email=".$email;
	$to = $email;
	$subject = $name." has invited you to share expenses on RoomIOU";
	$mail_body = '<html>
	<body>
	     <div style="font-family:Arial,Helvetica,sans-serif; font-size:14px; color:#696762 !important"> 
	     <div style="background-color: #2c2c2c;">
		<img src="http://roomiou.com/img/Roomioularge.png" height="55" display:block/>
	     </div>
	    	<div style="background:url(http://roomiou.com/css/cssimg/darkbluebkg.png); background-color:#b0dfed; padding: 40px">
		<div style="background: #FFFFFF; max-width:400px; margin:0 auto; border:1px solid #CDCDCD">
		    <div style="padding: 40px;">
		    <p>Hi '.$invitename.', </p>
			<p>'.$name.' invites you to share expenses on RoomIOU. RoomIOU is an 
			expense tracker that allows you to split rooming costs easily with
			your roommates. Now, you no longer have to awkwardly ask them to split
			grocery costs or other living fees. On RoomIOU, you can bill IOUs with no sweat.</p>
			<p>Create your free account and join '.$name.'&#39;s room now!</p>
			<div style="margin-top: 10px; margin-bottom: 10px">
			<a href="'.$link.'" style="text-decoration: none !important"><span style="font-size: 20px; color:#2c2c2c; font-weight:200; font-family: Arial,Helvetica,sans-serif">JOIN ROOM<span style="color:#e6d61b;">IOU</span></span></a>
			</div>
			<p>Thank you,</p>
			<p style="display:inline;">The RoomIOU Team</p>
			<p>RoomIOU | www.roomiou.com</p>
		    </div>
		</div>
	    	</div>
	     </div>
	</body>
	</html>';

	$headers = "From: RoomIOU <roomiou@roomiou.com>\r\n";
	$headers .= "Reply-To: RoomIOU <support@roomiou.com>\r\n";
	$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
	$sent = mail($to, $subject, $mail_body, $headers);
	if ($sent) {
		echo '<span style="color: #898989;">An invitation has been sent to '.$email;
		echo '</span>';
		
		//Check if invite already sent
		$query = "SELECT invites FROM rooms WHERE id='$roomid'";
		$result = mysql_query($query) or die(mysql_error());
		$row = mysql_fetch_assoc($result);
		$invites = $row['invites'];
		
		//If invite does not already exist, add it
		if (!is_int(strpos($invites, $email))) {
			$emailDelimited = $email.',';
			$query = "UPDATE rooms SET invites=CONCAT('$invites','$emailDelimited') WHERE id='$roomid'";
			mysql_query($query) or die(mysql_error());
		}
	} else {
		echo '<span style="color: #898989;">Email failed for '.$email;
		echo '</span>';
	}
//}
?>