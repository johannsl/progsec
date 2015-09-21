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
        $sql  = "SELECT * FROM posts WHERE postId = $postId";
        $result = $this->db->query($sql); //VULN: SQL-Injection via postId variable (G21_0018)
        $row = $result->fetch();

        if($row === false) {
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
        return $this->db->exec(
            sprintf("DELETE FROM posts WHERE postid='%s';", $postId)); //VULN: SQL-Injection via postId variable (new Vulnerability)
    }


    public function save(Post $post)
    {
        $title   = $post->getTitle();
        $author = $post->getAuthor();
        $content = $post->getContent();
        $date    = $post->getDate();

        if ($post->getPostId() === null) {
            $query = "INSERT INTO posts (title, author, content, date) "
                . "VALUES ('$title', '$author', '$content', '$date')";
        }

        $this->db->exec($query);  //VULN: SQL-Injection via postId variable (G21_0018)
        return $this->db->lastInsertId(); //Bad-Practice: No erro check if insertion worked
    }
}
