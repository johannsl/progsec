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
    protected $salt;
    protected $email   = null;
    protected $bio     = 'Bio is empty.';
    protected $age;
    protected $bankAccNum = null;
    protected $isAdmin = 0;
    protected $isDoctor = 0; 
    protected $moneyReceived;
    protected $moneySpent;

    function __construct($username, $hash, $salt, $fullname, $address, $postcode, $moneySpent, $moneyReceived)
    {
        $this->username = $username;
        $this->hash = $hash;
        $this->salt = $salt;
        $this->fullname = $fullname;
        $this->address = $address;
        $this->postcode = $postcode;
        $this->moneySpent = $moneySpent;
		$this->moneyReceived = $moneyReceived;
    }

    public function getSalt()
    {
        return $this->salt;
    }

    public function setSalt($salt)
    {
        $this->salt = $salt;
        return $this;
    }

    // Getters
    public function getUserId() {
        return $this->userId;
    }

    public function getUsername() {
        return $this->username;
    }

    public function getHash() {
        return $this->hash;
    }

    public function getFullname() {
        return $this->fullname;
    }

    public function getAddress() {
        return $this->address;
    }

    public function getPostcode() {
        return $this->postcode;
    }

    public function getMoneySpent() {
        return $this->moneySpent;
    }

    public function getMoneyReceived() {
        return $this->moneyReceived;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getBio() {
        return $this->bio;
    }

    public function getAge() {
        return $this->age;
    }

	public function getBankAccNum() {
        return $this->bankAccNum;
    }

	public function isAdmin() {
        return $this->isAdmin === '1';
    }

    public function isDoctor() {
        return $this->isDoctor === '1';
    }

	// Setters
    public function setUserId($userId) {
        $this->userId = $userId;
        return $this;
    }

    public function setUsername($username) {
        $this->username = $username;
        return $this;
    }

    public function setHash($hash) {
        $this->hash = $hash;
        return $this;
    }

    public function setFullname($fullname) {
        $this->fullname = $fullname;
        return $this;
    }

    public function setAddress($address) {
        $this->address = $address;
        return $this;
    }

    public function setPostcode($postcode) {
        $this->postcode = $postcode;
		return $this;
    }

    public function setEmail($email) {
        $this->email = $email;
        return $this;
    }
    
    public function setBio($bio) {
        $this->bio = $bio;
        return $this;
    }

    public function setAge($age) {
        $this->age = $age;
        return $this;
    }

	public function setBankAccNum($bankAccNum) 	{
        $this->bankAccNum = $bankAccNum;
		return $this;
    }

    public function setIsAdmin($isAdmin) {
        $this->isAdmin = $isAdmin;
        return $this;
    }

    public function setIsDoctor($isDoctor) {
        $this->isDoctor = $isDoctor;
        return $this;
    }
}
