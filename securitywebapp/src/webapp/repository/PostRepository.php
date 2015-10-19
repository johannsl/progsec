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
    
    public static function create($id, $author, $title, $content, $date) //Bad-Practice: Should be private static
    {
        $post = new Post;
        
        return $post
            ->setPostId($id)
            ->setAuthor($author)
            ->setTitle($title)
            ->setContent($content)
            ->setDate($date);
    }

    public function find($postId)
    {
        //VULN: SQL-Injection via postId variable (G21_0018)
        // I believe this is fixed
        $sql  = "SELECT * FROM posts WHERE postId = :postId";
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
            $row['postId'],
            $row['author'],
            $row['title'],
            $row['content'],
            $row['date']
        );

       //  $this->db = $db;
    }

    public function deleteByPostid($postId)
    {
        //VULN: SQL-Injection via postId variable (new Vulnerability)
        // I believe this is fixed
        $sql = "DELETE FROM posts WHERE postid = :postId";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':postId', $postId);
        return $stmt->execute();
    }

    public function save(Post $post)
    {
        //VULN: SQL-Injection via postId variable (G21_0018)
        // I believe this is fixed
        if ($post->getPostId() === null) {
            $query = "INSERT INTO posts (title, author, content, date) "
                . "VALUES (:title, :author, :content, :date)";
            $stmt = $this->db->prepare($query);

            $title   = $post->getTitle();
            $author = $post->getAuthor();
            $content = $post->getContent();
            $date    = $post->getDate();

            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':author', $author);
            $stmt->bindParam(':content', $content);
            $stmt->bindParam(':date', $date);
            $stmt->execute();
        }

        return $this->db->lastInsertId(); //Bad-Practice: No erro check if insertion worked
    }
}
