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
	if (p.length < 8) {
		message = "Password must be at least 8 characters."; //upper boundary?
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
		message = "Password must contain at least two lower case letter.";
		return false;
	}
	if (count_upper < 1) {
		message = "Password must contain at least one upper case letter.";
		return false;
	}
	if (count_other < 2) {
		message = "Password must contain at least two special symbols (e.g numbers or _+,... etc).";
		return false;
	}
	message = "Password is ok";
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