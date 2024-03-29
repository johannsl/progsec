<?php

namespace tdt4237\webapp\models;

class Post
{
    protected $postId;
    protected $author;
    protected $title;
    protected $content;
    protected $date;
    protected $pay;
    protected $answerByDoctor;


    public function getPostId() {
        return $this->postId;
    }

    public function setPostId($postId) {
        $this->postId = $postId;
        return $this;
    }

    public function getAuthor() {
        return $this->author;
    }

    public function setAuthor($author) {
        $this->author = $author;
        return $this;
    }

    public function getDate() {
        return $this->date;
    }

    public function setDate($date) {
        $this->date = $date;
        return $this;
    }

    public function getContent() {
        return $this->content;
    }

    public function setContent($content) {
        $this->content = $content;
        return $this;
    }

    public function setTitle($title) {
        $this->title = $title;
        return $this;
    }

    public function getTitle() {
        return $this->title;
    }

    public function setPay($pay) {
        $this->pay = $pay;
        return $this;
    }
    
    public function getPay() {
        return $this->pay;
    }

    public function setAnswerByDoctor($answerByDoctor){
        $this->answerByDoctor = $answerByDoctor;
        return $this;
    }

    public function getAnswerByDoctor(){
        return $this->answerByDoctor;
    }


}
