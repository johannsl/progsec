<?php

namespace tdt4237\webapp\validation;

class EditUserFormValidation
{
    private $validationErrors = [];
    
    public function __construct($email, $bio, $age, $bankAccNum)
    {
        $this->validate($email, $bio, $age, $bankAccNum);
    }
    
    public function isGoodToGo()
    {
        return \count($this->validationErrors) === 0;
    }
    
    public function getValidationErrors()
    {
        return $this->validationErrors;
    }

    private function validate($email, $bio, $age, $bankAccNum)
    {
        $this->validateEmail($email);
        $this->validateAge($age);
        $this->validateBio($bio);
		$this->validateBankAccNum($bankAccNum);
    }
    
    private function validateEmail($email)
    {
        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->validationErrors[] = "Invalid email format on email";
        }
    }
    
    private function validateAge($age)
    {
        if (! is_numeric($age) or $age < 0 or $age > 130) {
            $this->validationErrors[] = 'Age must be between 0 and 130.';
        }
    }

    private function validateBio($bio)
    {
        if (empty($bio)) {
            $this->validationErrors[] = 'Bio cannot be empty';
        }
    }
	
	private function validateBankAccNum($bankAccNum)
    {
		// think this through
		if (strlen($bankAccNum) > 50) // this is our VARCHAR limit
		{
			$this->validationErrors[] = 'Bank account number cannot be longer then 50 characters';
		}
		
		if (!(ctype_alnum($bankAccNum)) && !(empty($bankAccNum))) // this should test if the bankAccNum consists only of letters and/or numbers or is empry
		{
			$this->validationErrors[] = 'Bank account number can contain only numbers and/or letters';
		}
    }
}
