<?php

namespace tdt4237\webapp;

use tdt4237\webapp\models\User;

class Sql
{
    static $pdo;

    function __construct()
    {
    }

    /**
     * Create tables.
     */
    static function up()
    {
        $q1 = "CREATE TABLE users (id INTEGER PRIMARY KEY, user VARCHAR(50), pass VARCHAR(50), salt VARCHAR(20), email varchar(50) default null, full_name varchar(50), address varchar(50), postcode varchar (4), age varchar(50), bio varhar(50), is_admin INTEGER, bank_acc_num VARCHAR(50) default null, money_received INTEGER DEFAULT 0, money_spent INTEGER DEFAULT 0, is_doctor INTEGER DEFAULT 0);";
        $q6 = "CREATE TABLE posts (post_id INTEGER PRIMARY KEY AUTOINCREMENT, author TEXT, title TEXT NOT NULL, content TEXT NOT NULL, date TEXT NOT NULL, pay INTEGER, answer_by_doctor INTEGER DEFAULT 0, lock_user varchar(50), lock_tstamp DATE, FOREIGN KEY(author) REFERENCES users(user));"; #lock_user should be foreign key?
        $q7 = "CREATE TABLE comments(comment_id INTEGER PRIMARY KEY AUTOINCREMENT, date TEXT NOT NULL, author TEXT NOT NULL, text INTEGER NOT NULL, belongs_to_post INTEGER NOT NULL, FOREIGN KEY(belongs_to_post) REFERENCES posts(post_id));";

        self::$pdo->exec($q1);
        self::$pdo->exec($q6);
        self::$pdo->exec($q7);

        print "[tdt4237] Done creating all SQL tables.".PHP_EOL;

        self::insertDummyUsers();
        self::insertPosts();
        self::insertComments();
    }

    static function insertDummyUsers()
    {
        $salt1 = Hash::random_salt();
        $salt2 = Hash::random_salt();
        $salt3 = Hash::random_salt();
        $salt4 = Hash::random_salt();


        $hash1 = Hash::make(bin2hex(openssl_random_pseudo_bytes(10)), $salt1);
        $hash2 = Hash::make(bin2hex(openssl_random_pseudo_bytes(10)), $salt2);
        $hash3 = Hash::make(bin2hex(openssl_random_pseudo_bytes(10)), $salt3);
        $hash4 = Hash::make('Testuser123', $salt4);

		$q1 = "INSERT INTO users(user, pass, salt, is_admin, full_name, address, postcode, bank_acc_num, money_received, money_spent , is_doctor) VALUES ('admin', '$hash1', $salt1,  1, 'admin', 'homebase', '9090', NULL, 0, 0, 0)";
        $q2 = "INSERT INTO users(user, pass, salt, is_admin, full_name, address, postcode, bank_acc_num, money_received, money_spent , is_doctor) VALUES ('bob', '$hash2', $salt2,  1, 'Robert Green', 'Greenland Grove 9', '2010', NULL, 0, 0, 0)";
        $q3 = "INSERT INTO users(user, pass, salt, is_admin, full_name, address, postcode, bank_acc_num, money_received, money_spent , is_doctor) VALUES ('bjarni', '$hash3', $salt3,  1, 'Bjarni Torgmund', 'Hummerdale 12', '4120', NULL, 0, 0, 0)";
        $q4 = "INSERT INTO users(user, pass, salt, is_admin, full_name, address, postcode, bank_acc_num, money_received, money_spent , is_doctor) VALUES ('testuser', '$hash4', $salt4,  1, 'Teaching Assistant', 'NTNU', '7000', NULL, 0, 0, 0)";
		
        self::$pdo->exec($q1);
        self::$pdo->exec($q2);
        self::$pdo->exec($q3);
        self::$pdo->exec($q4);


        print "[tdt4237] Done inserting dummy users.".PHP_EOL;
    }

    static function insertPosts() {
        $q4 = "INSERT INTO posts(author, date, title, content, pay, answer_by_doctor, lock_user, lock_tstamp) VALUES ('bob', '26082015', 'I have a problem', 'I have a generic problem I think its embarrasing to talk about. Someone help?', 1, 1, '', 0)";
        $q5 = "INSERT INTO posts(author, date, title, content, pay, answer_by_doctor, lock_user, lock_tstamp) VALUES ('bjarni', '26082015', 'I also have a problem', 'I generally fear very much for my health', 0, 0, '', 0)";

        self::$pdo->exec($q4);
        self::$pdo->exec($q5);
        print "[tdt4237] Done inserting posts.".PHP_EOL;

    }

    static function insertComments() {
        $q1 = "INSERT INTO comments(author, date, text, belongs_to_post) VALUES ('bjarni', '26082015', 'Don''t be shy! No reason to be afraid here',0)";
        $q2 = "INSERT INTO comments(author, date, text, belongs_to_post) VALUES ('bob', '26082015', 'I wouldn''t worry too much, really. Just relax!',1)";
        self::$pdo->exec($q1);
        self::$pdo->exec($q2);
        print "[tdt4237] Done inserting comments.".PHP_EOL;


    }

    static function down()
    {
        $q1 = "DROP TABLE users";
        $q4 = "DROP TABLE posts";
        $q5 = "DROP TABLE comments";



        self::$pdo->exec($q1);
        self::$pdo->exec($q4);
        self::$pdo->exec($q5);

        print "[tdt4237] Done deleting all SQL tables.".PHP_EOL;
    }
}
try {
    // Create (connect to) SQLite database in file
    Sql::$pdo = new \PDO('sqlite:app.db');
    // Set errormode to exceptions
    Sql::$pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
} catch (\PDOException $e) {
    echo $e->getMessage();
    exit();
}
