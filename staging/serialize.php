<?php
	include_once("../php/connect.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

</head>

<body>
	<p>Serializing all transaction splits...</p>
	<?php
	$query = "SELECT * FROM transactions";
	$result = mysql_query($query) or die(mysql_error());
	while ($row = mysql_fetch_array($result)) {
		$id = $row['id'];
		$split = $row['split'];
		echo 'Original split: '.$split.' at ID: '.$id.'<br>';
		$array = unserialize($split);
		if (is_array($array)) {
			echo 'Already serialized, so don\'t do anything with it. <br><br>';
		} else {
			$arr = explode(",", $split);
			$newArr = array();
			for ($i=1; $i<count($arr); $i+=2) {
				$key = $arr[$i-1];
				$value = $arr[$i];
				$newArr[$key] = $value;
			}

			$newSplit = serialize($newArr);

			echo 'Serialized the array: '.$newSplit.'<br>';
			$query2 = "UPDATE transactions SET split='$newSplit' WHERE id='$id'";
			mysql_query($query2) or die(mysql_error());
			echo 'Updated the array. <br><br>';
		}
	}

	?>
	<p>Done.</p>
</body>
</html>