<?php
include_once("php/connect.php");
include_once("php/functions.php");
if (!isset($_SESSION['user_id'])) {
	header('Location: index.php');
}
$user = $_SESSION['user_id'];
$room = $_SESSION['room_id'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>RoomIOU</title>

<link rel="stylesheet" type="text/css" href="css/reset.css"/>
<link rel="stylesheet" type="text/css" href="css/960_24_col.css"/>
<link rel="stylesheet" type="text/css" href="css/style.css"/>
<link rel="stylesheet" type="text/css" href="css/summary.css"/>

<script src="http://cdn.jquerytools.org/1.2.7/full/jquery.tools.min.js"></script>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js" type="text/javascript"></script>
<script src="js/modernizr.js"></script>
<script src="js/ajax.js"></script>
<script type="text/javascript">
$(function() {
	// check placeholder browser support
	if (!Modernizr.input.placeholder) {	    
		// set placeholder values
		$(this).find('[placeholder]').each(function(){
			if ($(this).val() == '') {
				$(this).val( $(this).attr('placeholder') );
			}
		});
				        
		// focus and blur of placeholders
		$('[placeholder]').focus(function(){
			if ($(this).val() == $(this).attr('placeholder')) {
				$(this).val('');
				$(this).removeClass('placeholder');
			}
		}).blur(function(){
			if ($(this).val() == '' || $(this).val() == $(this).attr('placeholder')) {
				$(this).val($(this).attr('placeholder'));
				$(this).addClass('placeholder');
			}
		});
				        
		// remove placeholders on submit
		$('[placeholder]').closest('form').submit(function() {
			$(this).find('[placeholder]').each(function() {
				if ($(this).val() == $(this).attr('placeholder')) {
					$(this).val('');
				}
			})
		});
	}
});

$.noConflict();

$(document).ready(function() {
	$(".transaction").tooltip();
});
</script>
</head>

<body>
<div id="wrapper">
    <div id="header">
        <div id="headerContainer" class="container_24">
        	<a href="summary.php"><img src="img/Roomioularge.png" /></a>
            <div id="headerContent">
            	<a href="settings.php">Settings</a>
            	<a href="feedback.php">Feedback</a>
            	<a href="php/logout.php">Logout</a>
            </div>
        </div>
    </div>
    
    <div id="bodyWrapper" class="container_24">
    	<div class="grid_6">
	<div id="sidebody">
	    <div id="sidebodyContent">
            	<h3></h3>
                <ul>
                <?php
					$roommates = getRoommates($room);
					for ($i=0; $i<count($roommates); $i++) {
						$uid = $roommates[$i];
						$name = getName($uid);
						$balance = number_format(getBalance($uid), 2);
						if ($balance < 0) {
							$balance = substr_replace($balance, "", 0, 1);
							echo '<li class="red"><p class="name">'.$name.'</p><p><span class="small">owes</span> $'.$balance.'</p></li>';
						} else if ($balance > 0) {
							echo '<li class="green"><p class="name">'.$name.'</p><p><span class="small">gets back</span> $'.$balance.'</p></li>';	
						} else {
							echo '<li class="grey"><p class="name">'.$name.'</p><p><span class="small">is even</span> $'.$balance.'</p></li>';	
						}
					}
					
					
				?>
                </ul>
                
                <button type="button" id="invitebutton" onclick="window.location.href='settings.php?go=room'">Invite Roommates</button>
            </div>
	</div>
	<div id="feedback"><a href="feedback.php">Send us feedback!</a></div>
	</div>
		<div class="grid_18">
	   		<div id="nav" class="roundCorner">
            	<a href="summary.php" class="selected">Summary</a>
            	<a href="addbill.php">Add Bill</a>
            	<a href="addpayment.php">Add Payment</a>
        	</div>
            
            <div id="body" class="roundCorner">
            <?php
                $query = "SELECT * FROM transactions WHERE roomid='".$room."' ORDER BY date DESC";
                $result = mysql_query($query) or die(mysql_error());
                
                //If there are no transactions, display placeholder text. Otherwise, display transactions
                if (mysql_num_rows($result) == 0) {
                	echo '<span id="placeholdertext"><span style="font-weight: bold;">Welcome to RoomIOU!</span></br></br>
						No transactions have been added yet. To bill a roommate, click "Add Bill" in the navigation bar above.</span>';
                } else {
	                $lastColor = 'dark';
	                $monthDisplayed = '';
	                while ($row = mysql_fetch_assoc($result)) {
	                    $date = date("n/j",$row['date']);
	                    $month = date("n",$row['date']);
	                    $type = $row['type'];
	                    if ($type == 'bill') {
	                    	$purchaser = getName($row['purchaser']);
	                    } else if ($type == 'payment') {
	                    	$purchaser = getName($row['payer']);
	                    }
	                    $item = $row['item'];
	                    $total = number_format($row['total'], 2);
	                    $id = $row['id'];
	                    
	                    //If the month has not yet been displayed, display it with the year
	                    if ($monthDisplayed != $month) {
	                        $monthName = date("F",$row['date']);
	                        $year = date("Y",$row['date']);
	                        echo '<span class="header">'.$monthName.' '.$year.'</span>';
	                        $monthDisplayed = $month;
	                        $class = 'light'; //First transaction under a month heading is always light
	                    } else { //Determine alternating light or dark if not directly under a month heading
	                        if ($lastColor == 'dark') {
	                            $class = 'light';
	                        } else {
	                            $class = 'dark';
	                        }
	                    }
	                    $lastColor = $class;
	                    
	                    //Print the transaction HTML
	                    echo '<span class="transaction '.$class.'" onclick="location.href=&#39;edit.php?id='.$id.'&type='.$type.'&#39;">';
	                    echo '<span class="date">'.$date.'</span>';
	                    echo '<span class="item">'.$item.'</span>';
	                    echo '<span class="purchaser">'.$purchaser.'</span>';
	                    echo '<span class="total">$'.$total.'</span></span>';
	                    
	                    //Print the tooltip HTML
						$split = getSplit($id);
						echo '<div class="tooltip">';
						echo '<p class="large"><strong>Transaction details</strong></p>';
						echo '<p>Item: '.$item.'</p>';
						echo '<br>';
						if ($type == 'bill') {
							echo '<p><u><strong>Expense split</strong></u></p>';
						} else if ($type == 'payment') {
							echo '<p><u><strong>Payment</strong></u></p>';
						}						
						foreach ($split as $key => $value) {
							$name = getName($key);
							echo '<p>'.$name.': $'.$value.'</p>';
						}
						echo '</div>';
                	}
                }
            ?>
            	<span style="font-size: 11px; color: #898989;">
            		</br>Hover over a transaction to view details.
                    </br>Click on a transaction to edit or delete.
                </span>
            </div>
	</div>

    </div>

	<div id="push"></div>
</div>
</body>
</html>