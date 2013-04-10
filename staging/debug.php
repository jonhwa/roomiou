<?php
	include_once("../php/connect.php");
	include_once("../php/functions.php");
	$room = $_GET['id'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

</head>

<body>
	<div id="input">
		<form method="get" action="debug.php">
			<label>Room ID</label>
			<input type="text" name="id" <?php echo 'value="'.$room.'"'; ?> />
			<button type="submit">Debug</button>
		</form>
	</div>
	<div id="output">
		<?php
			if ($room == '') {
				echo '<p>Enter a room ID to debug</p>';
			} else {
				$roommates = getRoommates($room);
				$trueBalance = sumBalance($room);
				echo '<h3>Balances</h3>';
				for ($i = 0; $i < count($roommates); $i++) {
					$name = getName($roommates[$i]);
					$balance = getBalance($roommates[$i]);
					echo '<p>'.$name.' has a balance of $'.$balance.', which should be $'.$trueBalance[$roommates[$i]].'.</p>';
				}

				echo '</br>';
				/*echo '<h3>Transactions</h3>';
				$query = "SELECT * FROM transactions WHERE roomid='$roomid'";
				$result = mysql_query($query) or die(mysql_error());
				while ($row = mysql_fetch_assoc($result)) {

				}*/	
			}
		?>
	</div>
</body>
</html>