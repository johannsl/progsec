<?php

namespace tdt4237\webapp;

use Symfony\Component\Config\Definition\Exception\Exception;

class Hash
{

    static $salt = "1234"; //VULN: usage of weak salt (too short and numbers => easy to bruteforce). NEW VULN_ID (although we discovered it in blackbox testing we didnt assign an id to the sheet)


    public function __construct()
    {
    }

    public static function make($plaintext)
    {
        return hash('sha1', $plaintext . Hash::$salt);

    }

    public function check($plaintext, $hash)
    {
        return $this->make($plaintext) === $hash;
    }

}
