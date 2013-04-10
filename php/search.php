<?php
include_once("connect.php");
include_once("functions.php");

$text = $_GET['text'];
$query = "SELECT * FROM rooms WHERE name LIKE '$text%'";
$result = mysql_query($query) or die(mysql_error());
while ($row = mysql_fetch_assoc($result)) {
	$roomid = $row['id'];
	$name = $row['name'];
	echo '<p onclick="select(\''.$roomid.'\',\''.$name.'\');">'.$name.'</p>';
}
?>