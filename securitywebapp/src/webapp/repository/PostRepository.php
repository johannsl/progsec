<?php

namespace tdt4237\webapp\repository;

use PDO;
use tdt4237\webapp\models\Post;
use tdt4237\webapp\models\PostCollection;

class PostRepository
{

    /**
     * @var PDO
     */
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }
    
    public static function create($id, $author, $title, $content, $date, $pay, $answerByDoctor) //Bad-Practice: Should be private static
    {
        $post = new Post;
        
        return $post
            ->setPostId($id)
            ->setAuthor($author)
            ->setTitle($title)
            ->setContent($content)
            ->setDate($date)
            ->setPay($pay)
            ->setAnswerByDoctor($answerByDoctor);
    }

    public function find($postId)
    {
        //VULN: SQL-Injection via postId variable (G21_0018)
        // I believe this is fixed
        $sql  = "SELECT * FROM posts WHERE post_id = :postId";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':postId', $postId);
        $row = false;
        if(!$stmt->execute() || ($row=$stmt->fetch()) === false) {
            return false;
        }
        return $this->makeFromRow($row);
    }

    public function all()
    {
        $sql   = "SELECT * FROM posts";
        $results = $this->db->query($sql);

        if($results === false) {
            return [];
            throw new \Exception('PDO error in posts all()');
        }

        $fetch = $results->fetchAll();
        if(count($fetch) == 0) {
            return false;
        }

        return new PostCollection(
            array_map([$this, 'makeFromRow'], $fetch)
        );
    }

    public function makeFromRow($row)
    {
        return static::create(
            $row['post_id'],
            $row['author'],
            $row['title'],
            $row['content'],
            $row['date'],
            $row['pay'],
            $row['answer_by_doctor']
            //$row['lock_tstamp']
        );
    }

    public function deleteByPostid($postId)
    {
        //VULN: SQL-Injection via postId variable (new Vulnerability)
        // I believe this is fixed
        $sql = "DELETE FROM posts WHERE post_id = :postId";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':postId', $postId);
        return $stmt->execute();
    }

    public function save(Post $post)
    {
        //VULN: SQL-Injection via postId variable (G21_0018)
        // I believe this is fixed
        if ($post->getPostId() === null) {
            $query = "INSERT INTO posts (title, author, content, date, pay, lock_user, lock_tstamp) "
                . "VALUES (:title, :author, :content, :date, :pay, '', 0)";
            $stmt = $this->db->prepare($query);

            $title   = $post->getTitle();
            $author = $post->getAuthor();
            $content = $post->getContent();
            $date    = $post->getDate();
            $pay    = $post->getPay();

            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':author', $author);
            $stmt->bindParam(':content', $content);
            $stmt->bindParam(':date', $date);
            $stmt->bindParam(':pay', $pay);
            $stmt->execute();
        }

        return $this->db->lastInsertId(); //Bad-Practice: No erro check if insertion worked
    }

    public function answeredByDoctor($postId) {
        $query = "UPDATE posts SET answer_by_doctor=1 WHERE post_id=:post_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':post_id', $postId);
        return $stmt->execute();
    }
    public function acquireLock($postId, $doctor) {
        $sql = "UPDATE posts SET lock_user=:doctor, lock_tstamp=" . time() . " WHERE post_id=:postid AND (lock_user='' OR lock_user=:doctor OR lock_tstamp<" . (time()-1800) . ")";
        $stmnt = $this->db->prepare($sql);
        $stmnt->bindParam(":doctor", $doctor);
        $stmnt->bindParam(":postid", $postId);
        $stmnt->execute();
        $num = $stmnt->rowCount();
        return ($num == 1);
    }
    public function releaseLock($postId, $doctor) {
        $sql = "UPDATE posts SET lock_user='', lock_tstamp=0 WHERE post_id=$postId AND (lock_user='' OR lock_user=$doctor OR lock_tstamp<NOW()-1800)";
        $stmnt = $this->db->prepare($sql);
        $stmnt->execute();
        $num = $stmnt->rowCount();
        return ($num == 1);
    }
}
