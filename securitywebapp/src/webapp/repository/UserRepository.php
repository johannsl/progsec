<?php

namespace tdt4237\webapp\repository;

use PDO;
use tdt4237\webapp\models\Age;
use tdt4237\webapp\models\Email;
use tdt4237\webapp\models\NullUser;
use tdt4237\webapp\models\User;

class UserRepository
{
    const INSERT_QUERY   = "INSERT INTO users(user, pass, email, age, bio, is_admin, full_name, address, postcode) VALUES('%s', '%s', '%s' , '%s' , '%s', '%s', '%s', '%s', '%s')";
    const UPDATE_QUERY   = "UPDATE users SET email='%s', age='%s', bio='%s', is_admin='%s', full_name ='%s', address = '%s', postcode = '%s', bank_acc_num = '%s', is_doctor = '%s' WHERE id='%s'";
    const FIND_BY_NAME   = "SELECT * FROM users WHERE user='%s'";		
    const DELETE_BY_NAME = "DELETE FROM users WHERE user='%s'";
    const SELECT_ALL     = "SELECT * FROM users";
    const FIND_FULL_NAME   = "SELECT * FROM users WHERE user='%s'";
    const MONEY_PAYMENT   = "UPDATE users SET money_spent=money_spent+:amount WHERE user=:sourceUser";
    const MONEY_PAYMENT_RECEIVED = "UPDATE users SET money_received=money_received+:amount WHERE user=:targetUser";

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
        $user = new User($row['user'], $row['pass'], $row['fullname'], $row['address'], $row['postcode'], $row['money_spent'], $row['money_received']);
        $user->setUserId($row['id']);
        $user->setFullname($row['full_name']);
        $user->setAddress(($row['address']));
        $user->setPostcode((($row['postcode']))); 
        $user->setBio($row['bio']);
        $user->setIsAdmin($row['is_admin']);
		$user->setBankAccNum($row['bank_acc_num']); 
        $user->setIsDoctor($row['is_doctor']); 

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
        // I DONT BELIEVE THIS METHOD IS EVER CALLED!
        echo "getNameByUsername was called!";
        echo $username;
        
        // username should be filtered	
        // I believe this is fixed
        $query = sprintf(self::FIND_FULL_NAME, $username);
        echo $username;
        $stmt = $this->db->prepare($query);
        //$result = $this->pdo->query($query, PDO::FETCH_ASSOC);	
        $row = $result->fetch();
        #return $row['fullname'];
        return false;
    }

    public function findByUser($username)
    {
        // username should be filtered
        // I believe this is fixed
        $query = "SELECT * FROM users WHERE user = :username";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':username', $username);
        $row = false;
        if(!$stmt->execute() || ($row=$stmt->fetch()) === false) {
            return false;
        }

        return $this->makeUserFromRow($row);

    }

    public function deleteByUsername($username)
    {
        // username should be filtered
        // I believe this is fixed
        if($this->findByUser($username)){
            $query = "DELETE FROM users WHERE user = :username";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            return 1;
        }
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
        // These values should be sanitized
        // I believe this is fixed
        $query = (
            "INSERT INTO users VALUES(:userid, :username, :hash, :email, :fullname, :address, :postcode, :age, :bio, :admin)"
        );
        $stmt = $this->pdo->prepare($query);
        
        $userid = $user->getUserId();
        $username = $user->getUsername();
        $hash = $user->getHash();
        $email = $user->getEmail();
        $age = $user->getAge();
        $bio = $user->getBio();
        $admin = $user->isAdmin();
        $fullname = $user->getFullname();
        $address = $user->getAddress();
        $postcode = $user->getPostcode();

        $stmt->bindParam(':userid', $userid);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':hash', $hash);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':age', $age);
        $stmt->bindParam(':bio', $bio);
        $stmt->bindParam(':admin', $admin);
        $stmt->bindParam(':fullname', $fullname);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':postcode', $postcode);

        return $stmt->execute();
    }

    public function saveExistingUser(User $user)
    {
        // These values should be sanitized
        // I believe this is fixed
        $query = (
            "UPDATE users SET email=:email, age=:age, bio=:bio, isadmin=:admin, fullname=:fullname, address=:address, postcode=:postcode, bank_acc_num=;bank_acc_num, is_doctor=:is_doctor WHERE id=:userid"
        );
        $stmt = $this->pdo->prepare($query);

        $email = $user->getEmail();
        $age = $user->getAge();
        $bio = $user->getBio();
        $admin = $user->isAdmin();
        $fullname = $user->getFullname();
        $address = $user->getAddress();
        $postcode = $user->getPostcode();
        $bank_acc_num = $user->getBankAccNum();
        $is_doctor = $user->isDoctor();
        $userid = $user->getUserId();

        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':age', $age);
        $stmt->bindParam(':bio', $bio);
        $stmt->bindParam(':admin', $admin);
        $stmt->bindParam(':fullname', $fullname);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':postcode', $postcode);
        $stmt->bindparam(':bank_acc_num', $bank_acc_num);
        $stmt->bindParam(':is_doctor', $is_doctor);
        $stmt->bindParam(':userid', $userid);
         
        return $stmt->execute();
    }

    //Savly transfer money between users. Does not set the data on the user but updates the db directly, to prevent raice conditions
    //the receivedMoney and SpentMoney values are only updated with this function, not when a user is normally saved to guarantee data consistentcy
    public function payMoney($sourceUsername, $targetUsername, $amount_of_money) {

	//we have to do the update in a transaction, because otherwise an concurrent moneytransaction could falsify the results
	$targetUser = $this->findByUser($targetUsername);
	$sourceUser = $this->findByUser($sourceUsername);
	if($targetUser == $sourceUser || $targetUser == false || $sourceUser == false || strlen($sourceUser->getBankAccNum()) < 1 || 
        (!ctype_digit($amount_of_money) && !is_int($amount_of_money)) || $amount_of_money <= 0) {
		return false;
	}
	//we calculate the updates directly in the database instead on setting them to the users and saving them to cirumvent race conditions
	$statement = $this->pdo->prepare(self::MONEY_PAYMENT);
    $statement->execute(array(':amount' => $amount_of_money, ':sourceUser' => $sourceUser->getUsername()));
    $statement = $this->pdo->prepare(self::MONEY_PAYMENT_RECEIVED);
	$statement->execute(array(':amount' => $amount_of_money-3, ':targetUser' => $targetUser->getUsername()));
    return true;
    }
	
		
}
