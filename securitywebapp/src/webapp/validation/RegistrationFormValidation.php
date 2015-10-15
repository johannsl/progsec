<?php

namespace tdt4237\webapp\validation;

use tdt4237\webapp\models\User;

class RegistrationFormValidation
{
    const MIN_USER_LENGTH = 3;
    
    private $validationErrors = [];
    
    public function __construct($username, $password, $fullname, $address, $postcode)
    {
        return $this->validate($username, $password, $fullname, $address, $postcode);
    }
    
    public function isGoodToGo()
    {
        return empty($this->validationErrors);
    }
    
    public function getValidationErrors()
    {
        return $this->validationErrors;
    }

    private function validate($username, $password, $fullname, $address, $postcode)
    {
		 if (passwordStrength($password) == 1) {
            $this->validationErrors[] = 'Password should be between 8-30 characters.';
        }
		 if (passwordStrength($password) == 2) {
            $this->validationErrors[] = 'Password should contain at least two lower case letters.';
        }
		 if (passwordStrength($password) == 3) {
            $this->validationErrors[] = 'Password should contain at least one upper case letter.';
        }
		 if (passwordStrength($password) == 4) {
            $this->validationErrors[] = 'Password should contain at least two other symbols (e.g. nubers or +_, etc).';
        }

        if(empty($fullname)) {
            $this->validationErrors[] = "Please write in your full name";
        }

        if(empty($address)) {
            $this->validationErrors[] = "Please write in your address";
        }

        if(empty($postcode)) {
            $this->validationErrors[] = "Please write in your post code";
        }

        if (strlen($postcode) != "4") {
            $this->validationErrors[] = "Post code must be exactly four digits";
        }

        if (preg_match('/^[A-Za-z0-9_]+$/', $username) === 0) {
            $this->validationErrors[] = 'Username can only contain letters and numbers';
        }
		 

		 if (strlen($username) > 30) {		//30 is probably a okay max length
			 $this->validationErrors[] = 'Username too long. Max 30 letters.';
		 }
		 //i figured we should check the other parameters for max length as well
		 if (strlen($fullname) > 100) {	
			 $this->validationErrors[] = 'Fullname too long. Max 100 letters.';
		 }
		 if (strlen($address) > 100) {		
			 $this->validationErrors[] = 'Address too long. Max 100 letters.';
		 }
    }
}


function passwordStrength($p) {
	if (strlen($p) < 8 || strlen($p) > 30) {
		//message = "password should be between 8-30 characters.";
		return 1;
	}
	$count_lower = 0;
	$count_upper = 0;
	$count_other = 0;
	
	for ($i = 0; $i < strlen($p); $i++) {
		$c = ord($p[$i]);
		if (isLowerCase($c)) {
			$count_lower += 1;
		}
		else if (isUpperCase($c)) {
			$count_upper += 1;
		}
		else if (other($c)) {
			$count_other += 1;
		}
	}
	if ($count_lower < 2) {
		//message = "password should contain at least two lower case letters.
		return 2;
	}
	if ($count_upper < 1) {
		//password should contain at least one upper case letters.
		return 3;
	}
	if ($count_other < 2) {
		//password should contain at least two other symbols (e.g numbers or _+,... etc).
		return 4;
	}
	//decent password";
	return 0;
	
}

function isLowerCase($c) {
	if ($c >= 97 && $c <= 122) {
		return true;
	}
	return false;
}

function isUpperCase($c) {
	if ($c >= 65 && $c <= 90) {
		return true;
	}
	return false;
}

function other($c) {
	if (($c >= 33 && $c <= 64) || ($c >= 91 && $c <= 96) || ($c >= 123 && $c <= 126)) {
		return true;
	}
	return false;
}
