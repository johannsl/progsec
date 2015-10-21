<?php

namespace tdt4237\webapp;

use Symfony\Component\Config\Definition\Exception\Exception;

class Hash
{
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
        return $this->strcmpConstTime($this->make($plaintext, $salt),$hash);
    }

    /**
     * This function implements a string comparison which does
     * not terminate on a mismatch but runs always till the end
     * of the longer strings so no timing attack is possible
     * by measuring the execution time 
     */
    public function strcmpConstTime($str1, $str2) {
        $not_null = $str1  != null && $str2 != null;
        $mismatch = false;
        $upperLengthBound = max(strlen($str1),strlen($str2));
        $lowerLengthBound = min(strlen($str1),strlen($str2));
        for($C=0; $C<$upperLengthBound; $C++) {
            if($C<$lowerLengthBound) {
                //both strings are defined, so we do a real comparison
                $mismatch = !((($str1[$C] === $str2[$C])) && !$mismatch);
            } else {
                //one string is not defined anymore, so it has to be a mismatch, but we compare a comparison anyway
                $tmp = $str1[$lowerLengthBound-1] === $str2[$lowerLengthBound-1];
                $mismatch = !(false && $mismatch); //always true, so mismatch stays true in case of string length mismatch
            }
        }

        return $not_null && !$mismatch;
    }

}