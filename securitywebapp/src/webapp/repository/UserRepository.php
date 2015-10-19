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
    const UPDATE_QUERY   = "UPDATE users SET email='%s', age='%s', bio='%s', isadmin='%s', fullname ='%s', address = '%s', postcode = '%s' WHERE id='%s'";
    const FIND_BY_NAME   = "SELECT * FROM users WHERE user='%s'";		
    const DELETE_BY_NAME = "DELETE FROM users WHERE user='%s'";
    const SELECT_ALL     = "SELECT * FROM users";
    const FIND_FULL_NAME   = "SELECT * FROM users WHERE user='%s'";

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
        $user = new User($row['user'], $row['pass'], $row['fullname'], $row['address'], $row['postcode']);
        $user->setUserId($row['id']);
        $user->setFullname($row['fullname']);
        $user->setAddress(($row['address']));
        $user->setPostcode((($row['postcode']))); 
        $user->setBio($row['bio']);
        $user->setIsAdmin($row['isadmin']);

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
            "UPDATE users SET email=:email, age=:age, bio=:bio, isadmin=:admin, fullname=:fullname, address=:address, postcode=:postcode WHERE id=:userid"
        );
        $stmt = $this->pdo->prepare($query);

        $email = $user->getEmail();
        $age = $user->getAge();
        $bio = $user->getBio();
        $admin = $user->isAdmin();
        $fullname = $user->getFullname();
        $address = $user->getAddress();
        $postcode = $user->getPostcode();
        $userid = $user->getUserId();

        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':age', $age);
        $stmt->bindParam(':bio', $bio);
        $stmt->bindParam(':admin', $admin);
        $stmt->bindParam(':fullname', $fullname);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':postcode', $postcode);
        $stmt->bindParam(':userid', $userid);
         
        return $stmt->execute();
    }

}
