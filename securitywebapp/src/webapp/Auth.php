<?php

namespace tdt4237\webapp;

use Exception;
use tdt4237\webapp\Hash;
use tdt4237\webapp\repository\UserRepository;

class Auth
{
    /**
     * @var Hash
     */
    private $hash;

    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(UserRepository $userRepository, Hash $hash)
    {
        $this->userRepository = $userRepository;
        $this->hash           = $hash;
    }

    public function checkCredentials($username, $password)
    {
        $user = $this->userRepository->findByUser($username);

        if ($user === false) {
            return false;
        }

        return $this->hash->check($password, $user->getHash());
    }

    /**
     * Check if is logged in.
     */
    public function check()
    {
        return isset($_SESSION['user']);
    }

    public function getUsername() {
        if(isset($_SESSION['user'])){
        return $_SESSION['user'];
        }
    }

    /**
     * Check if the person is a guest.
     */
    public function guest()
    {
        return $this->check() === false;
    }

    /**
     * Get currently logged in user.
     */
    public function user()
    {
        if ($this->check()) {
            return $this->userRepository->findByUser($_SESSION['user']);
        }

        throw new Exception('Not logged in but called Auth::user() anyway');
    }

    /**
     * Is currently logged in user admin?
     */
    public function isAdmin()
    {
        if ($this->check()) {
            
            return $_SESSION['isAdmin'];
        }

        throw new Exception('Not logged in but called Auth::isAdmin() anyway');
    }

    public function logout()
    {
        unset($_SESSION['user']);
        session_destroy(); // This is not a safe logout. G21_0003
    }

    /*
     * Generates a CSRV-Token by using a random function and returns it
     */
    public function generateCsrfToken() {
         $csrftoken='';
         try { //random_bytes may throw an exception when no method to generate randomness is avaiable, in this case us mt_rand
                $csrftoken = bin2hex(openssl_random_pseudo_bytes(15));
         } catch (Exception $e) {
                $csrftoken = mt_rand(10000,1000000);
        }

        return $csrftoken;
    }

}
