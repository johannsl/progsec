function checkPassword(p) {
	var feedback = document.getElementById("psw_strength");	
	if (passwordStrength(p.value)) {
		feedback.style.color = "green";
		feedback.innerHTML = message;
	} else {
		feedback.style.color = "red";
		feedback.innerHTML = message;
	}
	
}

function passwordStrength(p) {
	if (p.length < 8 || p.length > 50) {
		message = "psw should be between 8-50 characters."; //upper boundary?
		return false;
	}
	var count_lower = 0;
	var count_upper = 0;
	var count_other = 0;
	
	for (i = 0; i < p.length; i++) {
		c = p.charCodeAt(i);
		if (isLowerCase(c)) {
			count_lower += 1;
		}
		else if (isUpperCase(c)) {
			count_upper += 1;
		}
		else if (other(c)) {
			count_other += 1;
		}
	}
	if (count_lower < 2) {
		message = "password should contain at least two lower case letters.";
		return false;
	}
	if (count_upper < 1) {
		message = "password should contain at least one upper case letters.";
		return false;
	}
	if (count_other < 2) {
		message = "password should contain at least two other symbols (e.g numbers or _+,... etc).";
		return false;
	}
	message = "decent password";
	return true;	
}

function isLowerCase(c) {
	if ((c >= 97 && c <= 122) || c==230 || c==248 || c==229) {
		return true;
	}
	return false;
}

function isUpperCase(c) {
	if ((c >= 65 && c <= 90) || c==198 || c==216 || c==197) {
		return true;
	}
	return false;
}

function isNumber(c) {
	if (c >= 48 && c <= 57) {
		return true;
	}
	return false;
}

function other(c) {
	if ((c >= 33 && c <= 64) || (c >= 91 && c <= 96) || (c >= 123 && c <= 126)) {
		return true;
	}
	return false;
}