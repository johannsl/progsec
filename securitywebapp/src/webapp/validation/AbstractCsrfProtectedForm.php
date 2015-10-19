<?php

namespace tdt4237\webapp\validation;

abstract class AbstractCsrfProtectedForm
{
    protected $validationErrors = [];
    
    public function __construct($token)
    {
        $this->validateCsrfToken($token);
    }
	
	private function validateCsrfToken($token)
    {
		if ($token != $_SESSION['csrftoken']) 
		{
			$this->validationErrors[] = 'Invalid CSRF-Token. Please try login of and on again.';
		}
		
    }
}
