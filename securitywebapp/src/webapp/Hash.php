<?php

namespace tdt4237\webapp;

use Symfony\Component\Config\Definition\Exception\Exception;

class Hash
{
<<<<<<< HEAD
=======

    static $salt = "1234"; //VULN: usage of weak salt (too short and numbers => easy to bruteforce). NEW VULN_ID (although we discovered it in blackbox testing we didnt assign an id to the sheet)

>>>>>>> master
    public function __construct()
    {
    }

    public static function random_salt()
    {
        $salt = "";
        $x = 0;
        while ($x<10)
        {
            $salt .= rand(0,9);
            $x ++; 
        }
        return $salt;
    }

    public static function make($plaintext, $salt)
    {
        return hash('sha256', $plaintext . $salt);

    }

    public function check($plaintext, $hash, $salt)
    {
        return $this->make($plaintext, $salt) === $hash;
    }

}
