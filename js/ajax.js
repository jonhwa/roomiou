// JavaScript Document

function ajax() {
	var xmlhttp;
	if (window.XMLHttpRequest) {
		xmlhttp = new XMLHttpRequest(); //IE7+, FF, Chrome, Opera, Safari
	} else {
		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP"); //IE6, IE5
	}
	return xmlhttp;
}

function invite(roomid,userid) {
	var name = document.getElementById('invitename').value;
	var email = document.getElementById('inviteemail').value;
	var xmlhttp = ajax();
	var url = "../php/invite.php";
	url += "?roomid=" + roomid;
	url += "&userid=" + userid;
	url += "&name=" + name;
	url += "&email=" + email;
	url += "&sid=" + Math.random();
	xmlhttp.onreadystatechange = function() {
		if(xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			document.getElementById('inviteResult').innerHTML = xmlhttp.responseText;	
		}
	};
	xmlhttp.open("GET",url,true);
	xmlhttp.send(null);
}

//Currently not used
function search(text) {
	var xmlhttp = ajax();
	var url = "../php/search.php";
	url += "?text=" + text;
	url += "&sid=" + Math.random();
	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			document.getElementById('dropdown').innerHTML = xmlhttp.responseText;
		}
	};
	xmlhttp.open("GET",url,true);
	xmlhttp.send(null);
}

function validate(field, value) {
	var xmlhttp = ajax();
	var url = "../php/validate.php";
	url += "?field=" + field;
	url += "&value=" + value;
	url += "&sid=" + Math.random();

	var target = field + 'error';
	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			document.getElementById(target).innerHTML = xmlhttp.responseText;
		}
	};
	xmlhttp.open("GET",url,true);
	xmlhttp.send(null);
}

function removeRoommate(user, balance) {
	var xmlhttp = ajax();
	var url = "../php/removeroommate.php";
	url += "?user=" + user;
	url += "&balance=" + balance;
	url += "&sid=" + Math.random();
	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			document.getElementById('confirm').innerHTML = xmlhttp.responseText;
		}
	};
	xmlhttp.open("GET",url,true);
	xmlhttp.send(null);
	
	var element = document.getElementById(user);
	element.parentNode.removeChild(element);
}

//Recommended payment from user1 to user2
function recommendedPayment(user1, user2) {
	var xmlhttp = ajax();
	var url = "../php/recommendedpayment.php";
	url += "?type=roommate";
	url += "&user1=" + user1;
	url += "&user2=" + user2;
	url += "&sid=" + Math.random();
	
	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			document.getElementById('reviewSpan').innerHTML = xmlhttp.responseText;
		}
	};
	xmlhttp.open("GET",url,true);
	xmlhttp.send(null);
}

//Calculate balance-clearing payments for a user
function recommendedBalance(user, room) {
	var xmlhttp = ajax();
	var url = "../php/recommendedpayment.php";
	url += "?type=balance";
	url += "&user=" + user;
	url += "&room=" + room;
	url += "&sid=" + Math.random();
	
	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			document.getElementById('roommates').innerHTML = xmlhttp.responseText;
		}
	};
	xmlhttp.open("GET",url,true);
	xmlhttp.send(null);
}

function deleteTransaction(id) {
	var xmlhttp = ajax();
	var url = "../php/deletetransaction.php";
	url += "?id=" + id;
	url += "&sid=" + Math.random();
	
	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			location.replace("../summary.php");
		}
	};
	
	xmlhttp.open("GET",url,false);
	xmlhttp.send(null);
}