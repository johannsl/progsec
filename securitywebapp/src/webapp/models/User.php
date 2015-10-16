<?php

namespace tdt4237\webapp\models;

class User
{

    protected $userId  = null;
    protected $username;
    protected $fullname;
    protected $address;
    protected $postcode;
    protected $hash;
    protected $email   = null;
    protected $bio     = 'Bio is empty.';
    protected $age;
    protected $bankAccNum;
    protected $isAdmin = 0;
    protected $isDoctor = 0; 
    protected $moneyReceived;
    protected $moneySpent;
    protected $isdoctor;

    function __construct($username, $hash, $fullname, $address, $postcode, $moneySpent, $moneyReceived)
    {
        $this->username = $username;
        $this->hash = $hash;
        $this->fullname = $fullname;
        $this->address = $address;
        $this->postcode = $postcode;
		$this->moneyReceived = $moneyReceived;
        $this->moneySpent = $moneySpent;
        
    }

   // getters
    public function getUserId()
    {
        return $this->userId;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getHash()
    {
        return $this->hash;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getBio()
    {
        return $this->bio;
    }

    public function getAge()
    {
        return $this->age;
    }

    public function getFullname() {
        return $this->fullname;
    }
	
	public function getBankAccNum()
    {
        return $this->bankAccNum;
    }

	// setters
    public function setFullname($fullname) {
        $this->fullname = $fullname;
    }

    public function getAddress() {
        return $this->address;
    }

    public function setAddress($address) {
        $this->address = $address;
    }

    public function getPostcode() {
        return $this->postcode;

    }

    public function setPostcode($postcode) 
	{
        $this->postcode = $postcode;
		return $this;
    }

    public function setUserId($userId)
    {
        $this->userId = $userId;
        return $this;
    }

    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    public function setHash($hash)
    {
        $this->hash = $hash;
        return $this;
    }

    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    public function setBio($bio)
    {
        $this->bio = $bio;
        return $this;
    }

    public function setAge($age)
    {
        $this->age = $age;
        return $this;
    }

    public function getMoneySpent()
    {
        return $this->moneySpent;
    }

    public function getMoneyReceived()
    {
        return $this->moneyReceived;
    }

    public function setIsAdmin($isAdmin)
    {
        $this->isAdmin = $isAdmin;
        return $this;
    }

    public function setIsDoctor($isDoctor)
    {
        $this->isDoctor = $isDoctor;
        return $this;
    }
	
	public function setBankAccNum($bankAccNum) 
	{
        $this->bankAccNum = $bankAccNum;
		return $this;
    }
	
	public function isAdmin() 
    {
        return $this->isAdmin === '1';
    }

    public function isDoctor() 
    {
        return $this->isDoctor === '1';
    }

}
