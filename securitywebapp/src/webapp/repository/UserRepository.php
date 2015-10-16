<?php

namespace tdt4237\webapp\repository;

use PDO;
use tdt4237\webapp\models\Age;
use tdt4237\webapp\models\Email;
use tdt4237\webapp\models\NullUser;
use tdt4237\webapp\models\User;

class UserRepository
{
    const INSERT_QUERY   = "INSERT INTO users(user, pass, email, age, bio, isadmin, fullname, address, postcode) VALUES('%s', '%s', '%s' , '%s' , '%s', '%s', '%s', '%s', '%s')";
    const UPDATE_QUERY   = "UPDATE users SET email='%s', age='%s', bio='%s', isadmin='%s', fullname ='%s', address = '%s', postcode = '%s', bankAccNum = '%s', isDoctor = '%s' WHERE id='%s'";
    const FIND_BY_NAME   = "SELECT * FROM users WHERE user='%s'";		
    const DELETE_BY_NAME = "DELETE FROM users WHERE user='%s'";
    const SELECT_ALL     = "SELECT * FROM users";
    const FIND_FULL_NAME   = "SELECT * FROM users WHERE user='%s'";
    const MONEY_PAYMENT   = "UPDATE users SET moneySent=moneySent+:amount WHERE user=':sourceUser'; UPDATE users SET moneyReceived=moneyReceived+:amount WHERE user=':targetUser';";

    /**
     * @var PDO
     */
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function makeUserFromRow(array $row)
    {
        $user = new User($row['user'], $row['pass'], $row['fullname'], $row['address'], $row['postcode'], $row['moneySpent'], $row['moneyReceived']);
        $user->setUserId($row['id']);
        $user->setFullname($row['fullname']);
        $user->setAddress(($row['address']));
        $user->setPostcode((($row['postcode']))); 
        $user->setBio($row['bio']);
        $user->setIsAdmin($row['isadmin']);
		$user->setBankAccNum($row['bankAccNum']); 
        $user->setIsDoctor($row['isdoctor']); 

        if (!empty($row['email'])) {
            $user->setEmail(new Email($row['email']));
        }

        if (!empty($row['age'])) {
            $user->setAge(new Age($row['age']));
        }

        return $user;
    }

    public function getNameByUsername($username)
    {						
        $query = sprintf(self::FIND_FULL_NAME, $username);			#username should be filtered
		
        $result = $this->pdo->query($query, PDO::FETCH_ASSOC);	
        $row = $result->fetch();
        return $row['fullname'];

    }

    public function findByUser($username)
    {
        $query  = sprintf(self::FIND_BY_NAME, $username);			#username should be filtered
        $result = $this->pdo->query($query, PDO::FETCH_ASSOC);		
        $row = $result->fetch();
        
        if ($row === false) {
            return false;
        }


        return $this->makeUserFromRow($row);
    }

    public function deleteByUsername($username)
    {
        return $this->pdo->exec(
            sprintf(self::DELETE_BY_NAME, $username)				#username should be filtered
        );
    }



    public function all()
    {
        $rows = $this->pdo->query(self::SELECT_ALL);
        
        if ($rows === false) {
            return [];
            throw new \Exception('PDO error in all()');
        }

        return array_map([$this, 'makeUserFromRow'], $rows->fetchAll());
    }

    public function save(User $user)
    {
        if ($user->getUserId() === null) {
            return $this->saveNewUser($user);
        }

        $this->saveExistingUser($user);
    }

    public function saveNewUser(User $user)
    {
        $query = sprintf(
            self::INSERT_QUERY, $user->getUsername(), $user->getHash(), $user->getEmail(), $user->getAge(), $user->getBio(), $user->isAdmin(), $user->getFullname(), $user->getAddress(), $user->getPostcode()
        );		#these values should also be sanitized
		
		if($query) 
		{ 
			return $this->pdo->exec($query);
		} 
		else 
		{
			$error = $this->pdo->errno . ' ' . $this->pdo->error;
			echo $error; 
		}
    }

    public function saveExistingUser(User $user)
    { 
        $query = sprintf(
            self::UPDATE_QUERY, $user->getEmail(), $user->getAge(), $user->getBio(), $user->isAdmin(), $user->getFullname(), $user->getAddress(), $user->getPostcode(), $user->getBankAccNum(), $user->isDoctor(), $user->getUserId()
        );		#these values should also be sanitized

        if($query) 
		{ 
			return $this->pdo->exec($query);
		} 
		else 
		{
			$error = $this->pdo->errno . ' ' . $this->pdo->error;
			echo $error; 
		}
    }

    public function payMoney($sourceUsername, $targetUsername, $amount_of_money) {

	//we have to do the update in a transaction, because otherwise an concurrent moneytransaction could falsify the results
	$targetUser = $this->findByUser($targetUsername);
	$sourceUser = $thia->findByUser($sourceUsername);
	if($targetUser == $sourceUser || $targetUser == false || $sourceUser == false || strlen($sourceUser->getBankAccNum) < 0 || !ctype_digit($amount_of_money) || $amount_of_money < 0) {
		return false;
	}
	
	//we calculate the updates directly in the database instead on setting them to the users and saving them to cirumvent race conditions
	$statement = $this->pdo->prepare(MONEY_PAYMENT);
	return $statement->execute(array(':amount' => $amount_of_money, ':sourceUser' => $sourceUser, ':targetUser' => $targetUser));
    }
	
		
}
