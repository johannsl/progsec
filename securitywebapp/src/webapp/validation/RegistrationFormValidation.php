<?php

namespace tdt4237\webapp\validation;

use tdt4237\webapp\models\User;

class RegistrationFormValidation extends AbstractCsrfProtectedForm 

{
    const MIN_USER_LENGTH = 3;

    protected $validationErrors = [];

    public function __construct($username, $password, $fullname, $address, $postcode, $token)
    {  
        parent::__construct($token);
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
            $this->validationErrors[] = 'Password must be at least 8 characters.';
        }
		 if (passwordStrength($password) == 2) {
            $this->validationErrors[] = 'Password must contain at least two lower case letters.';
        }
		 if (passwordStrength($password) == 3) {
            $this->validationErrors[] = 'Password must contain at least one upper case letter.';
        }
		 if (passwordStrength($password) == 4) {
            $this->validationErrors[] = 'Password must contain at least two other symbols (e.g. nubers or +_, etc).';
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
        
        if (strlen($username) > 50) {		
    	    $this->validationErrors[] = 'Username too long. Max 30 letters.';
    	}

    	// I figured we should check the other parameters for max length as well
    	if (strlen($fullname) > 50) {	
    	    $this->validationErrors[] = 'Fullname too long. Max 50 letters.';
    	}

    	if (strlen($address) > 50) {		
    	    $this->validationErrors[] = 'Address too long. Max 50 letters.';
    	}
    }
}

function passwordStrength($p) {
    
    // Convert to chararray in order to keep unicode letters intact
	$chrArray = preg_split('//u',$p, -1, PREG_SPLIT_NO_EMPTY); 	if (sizeof($chrArray) < 8) {
		return 1;
	}

	$count_lower = 0;
	$count_upper = 0;
	$count_other = 0;
	
	foreach ($chrArray as $ch) {
		$c = uniord($ch);
		
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

		// Message = password should contain at least two lower case letters.
		return 2;
	}
	if ($count_upper < 1) {

		// Password should contain at least one upper case letters.
		return 3;
	}
	if ($count_other < 2) {

		// Password should contain at least two other symbols (e.g numbers or _+,... etc).
		return 4;
	}
	// Decent password
	return 0;
	
}

function isLowerCase($c) {
	if (($c >= 97 && $c <= 122) || $c == 230 || $c == 248 || $c == 229) {
		return true;
	}
	return false;
}

function isUpperCase($c) {
	if (($c >= 65 && $c <= 90) || $c == 198 || $c == 216 || $c == 197) {
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

// Partly copied from http://php.net/manual/en/function.ord.php#42778
function uniord($u) {
    $k = mb_convert_encoding($u, 'UCS-2LE', mb_detect_encoding($u));
    $k1 = ord(substr($k, 0, 1));
    $k2 = ord(substr($k, 1, 1));
    return $k2 * 256 + $k1;
}
