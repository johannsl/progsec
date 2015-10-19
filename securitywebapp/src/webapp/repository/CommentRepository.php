<?php

namespace tdt4237\webapp\repository;

use PDO;
use tdt4237\webapp\models\Comment;

class CommentRepository
{
	
    /**
     * @var PDO
     */
    private $db;

	//id input should be parametrized
    const SELECT_BY_ID = "SELECT * FROM moviereviews WHERE id = %s"; //never used, so should be removed

    public function __construct(PDO $db)
    {

        $this->db = $db;
    }

    public function save(Comment $comment)
    {

		// SQL injection (G21_0018)
        // I believe this is fixed
        if ($comment->getCommentId() === null) {
            $query = "INSERT INTO comments (author, text, date, belongs_to_post) VALUES (:author, :text, :date, :postid)";
            $stmt = $this->db->prepare($query);

            $author  = $comment->getAuthor();
            $text    = $comment->getText();
            $date = (string) $comment->getDate();
            $postid = $comment->getPost();

            $stmt->bindParam(':author', $author);
            $stmt->bindParam(':text', $text);
            $stmt->bindParam(':date', $date);
            $stmt->bindparam(':postid', $postid);
            return $stmt->execute();
        }
    }

   public function findByPostId($postId)
    {
        // SQL injection (G21_0018)
        // I believe this is fixed
        $query = "SELECT * FROM comments WHERE belongs_to_post = :postId";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':postId', $postId);
        $stmt->execute();
        $rows = $stmt->fetchAll();
        return array_map([$this, 'makeFromRow'], $rows);
    }

    public function makeFromRow($row)
    {
        $comment = new Comment;
        
        return $comment
            ->setCommentId($row['commentId'])
            ->setAuthor($row['author'])
            ->setText($row['text'])
            ->setDate($row['date'])
            ->setPost($row['belongs_to_post']);
    }
}
