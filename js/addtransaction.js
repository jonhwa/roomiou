// JavaScript Document

function load() {
	var roommates = document.getElementsByClassName('roommates');
	for (var i=0; i<roommates.length; i++) {
		var input = roommates[i];
		input.onkeyup = function() {
			var self = this;
			window.setTimeout(function() {
				changeAmount();
			}, 0);
		}
	}
	
	/*var inp = document.getElementById('tax');
	inp.onkeydown = function() {
		var self = this;
		window.setTimeout(function() {
			splitTaxTip();
		}, 0);
	}
	
	var inp2 = document.getElementById('tip');
	inp2.onkeydown = function() {
		var self = this;
		window.setTimeout(function() {
			splitTaxTip();
		}, 0);
	}*/
}

//Called to review the transaction. Checks if expense splits adds up to the total, proceeding to display a submit button if it does, and displaying error messages if it doesn't
function reviewAmounts() {
	var roommates = document.getElementsByClassName('roommates');
	var length = roommates.length;
	
	//Output the information in the "reviewSpan" element
	var html = '';
	var target = document.getElementById('reviewSpan');
	
	var total = document.getElementById('total').value;
	/*if (total == '') {
		//Call evenSplit() to put placeholder 0.00 in all applicable fields to avoid NaN's showing up in the reviewSpan
		evenSplit();
	}*/
	
	var item = document.getElementById('item').value;
	if (item == '' || total == '') {
		html += '<div class="error"><p>You have to fill out Item and Total</p></div>';
	} else {	
		html += '<p>Item: ' + item + '</p>';
		html += '<p>Total: $' + parseFloat(total).toFixed(2) + '</p>';
		html += '<p>Purchaser: ' + document.getElementById('op' + document.getElementById('purchaser').value).innerHTML + '</p><br>';
		html += '<p><u>Expense Split:</u></p>';
		for (var i=0; i<length; i++) {
			var name = document.getElementById('n' + i).title;
			var value = parseFloat(document.getElementById(i).value).toFixed(2);
			if (isNaN(value)) {
				var zero = 0;
				zero = zero.toFixed(2);
				value = zero;
				document.getElementById(i).value = value;
			}
			html += '<p>' + name + ': $' + value + '</p>';
		}
		
		//Check if the expense split adds up to the total
		var total = parseFloat(document.getElementById('total').value).toFixed(2);
		var cumulativeSum = getSplitSum();
		
		//If the split adds up to the total, let the user submit the information. Else, make them edit it first
		if (total == cumulativeSum) {
			html += '<button type="submit" name="submit" id="submit" class="submit">Add Bill</button>';
		} else {
			html += '</br><p style="color: red;">The expense split of $' + cumulativeSum + ' doesn\'t add up to the total of $' + total + '. ';
			var difference = (total - cumulativeSum).toFixed(2);
			if (difference > 0) {
				html += 'You need an additional $' + difference + '. ';
			} else {
				html += 'You have $' + Math.abs(difference).toFixed(2) + ' too much. ';
			}
			html += '<a onclick="checkAll(); evenSplit();">(split evenly)</a></p>';
		} 
	}
	//Output the HTML
	target.innerHTML = html;
}

//Splits the expense evenly between all roommates who are checked
function evenSplit() {
	var total = parseFloat(document.getElementById('total').value).toFixed(2);
	var zero = 0;
	zero = zero.toFixed(2);
	if (total < 0 || isNaN(total)) {
		document.getElementById('total').value = zero;
		total = zero;	
	}
	
	//Get all roommates who are checked
	var roommates = document.getElementsByClassName('roommates');
	var checkedRoommates = new Array();
	var length = roommates.length;
	for (i=0; i<length; i++) {
		checked = document.getElementById(i+'check').checked;
		
		//If checked, add to array to be evenly split later. If not, make sure the split is 0
		var element = document.getElementById(i);
		if (checked) {
			checkedRoommates.push(element);
		} else {
			element.value = zero;
		}
	}
	
	//Split the expense equally for those roommates who aer checked
	length = checkedRoommates.length;
	var cumulativeSum = 0;
	for (i=0; i<length; i++) {
		var amount = ((total - cumulativeSum) / (length - i)).toFixed(2);
		var element = checkedRoommates[i];
		element.value = amount;
		cumulativeSum += parseFloat(amount);
	}
	
	reviewAmounts();
}

//Check all roommates' boxes for an even split
function checkAll() {
	var roommates = document.getElementsByClassName('roommates');
	var length = roommates.length;
	for (i=0; i<length; i++) {
		document.getElementById(i+'check').checked = true;
	}
}

//Splits tax and tip proportionally according to expense
/*function splitTaxTip() {
	
}*/

//Get the sum of the expense split and return it
function getSplitSum() {
	var roommates = document.getElementsByClassName('roommates');
	var length = roommates.length;
	
	var cumulativeSum = 0;
	for (var i=0; i<length; i++) {
		var value = parseFloat(document.getElementById(i).value);
		
		//If there is no value, give it the value of 0
		if (isNaN(value)) value = 0;
		
		cumulativeSum += value;
	}
	
	return cumulativeSum.toFixed(2);
}

//Used in /addtransaction.php
function formatDecimal(inp) {
	var number = parseFloat(inp.value).toFixed(2);
	inp.value = number;	
}