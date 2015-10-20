<?php

namespace tdt4237\webapp\controllers;

use tdt4237\webapp\models\Post;
use tdt4237\webapp\controllers\UserController;
use tdt4237\webapp\models\Comment;
use tdt4237\webapp\validation\PostValidation;
use tdt4237\webapp\validation\CommentValidation;

class PostController extends Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $posts = $this->postRepository->all();

        $posts->sortByDate();
        $this->render('posts.twig', ['posts' => $posts]);
    }

    public function show($postId)
    {
        echo $postId;
        if ($this->auth->guest()) {
            echo $postId;
            $this->app->flash("info", "You must be logged in to do that");
            $this->app->redirect("/login");
        }else{
            echo $postId;
            $post = $this->postRepository->find($postId);
            $comments = $this->commentRepository->findByPostId($postId);
            $request = $this->app->request;

            $this->render('showpost.twig', [
                'post' => $post,
                'comments' => $comments,
            ]);
        }
        //$this->render('showpost.twig', [
        //    'post' => $post,
        //    'comments' => $comments,
        //    'flash' => $variables
        //]);

    }

    public function addComment($postId)
    {

        if(!$this->auth->guest()) {
            //now we save the comment with checking :-)
            $request = $this->app->request;
            $validation = new CommentValidation($request->post("text"), $postId, $request->post("csrftoken"));
            if ($validation->isGoodToGo()) {
                $comment = new Comment($request->post("text"));
                $comment->setAuthor($_SESSION['user']);
                $comment->setText($this->app->request->post("text"));
                $comment->setDate(date("dmY"));
                $comment->setPost($postId);
                $this->commentRepository->save($comment);
                $this->app->redirect('/posts/' . $postId); 
            } else {
                $this->app->flash('error', join("\n", $validation->getValidationErrors()));
                $this->app->redirect('/posts/' . $postId);
            }
        }
        else {
            $this->app->redirect('/login');
            $this->app->flash('info', 'you must log in to do that');
        }

    }

    public function showNewPostForm()
    {

        if ($this->auth->check()) {
            $username = $_SESSION['user'];
            $this->render('createpost.twig', ['username' => $username]);
        } else {

            $this->app->flash('error', "You need to be logged in to create a post");
            $this->app->redirect("/");
        }

    }

    public function create()
    {
        if ($this->auth->guest()) {
            $this->app->flash("info", "You must be logged on to create a post");
            $this->app->redirect("/login");
        } else {
            $request = $this->app->request;
            $title = $request->post('title');
            $content = $request->post('content');
            $author = $_SESSION['user'];
            $date = date("dmY");
            $validation = new PostValidation($author, $title, $content, $request->post('csrftoken'));
            if ($validation->isGoodToGo()) {
                $post = new Post();
                $post->setAuthor($author);
                $post->setTitle($title);
                $post->setContent($content);
                $post->setDate($date);
                $savedPost = $this->postRepository->save($post);
                $this->app->flash('info', 'Post succesfully posted');
                $this->app->redirect('/posts/' . $savedPost);
            }
        }

            $this->app->flashNow('error', join("\n", $validation->getValidationErrors()));
            $this->render('createpost.twig', ['username' => $_SESSION['user']]);
            // RENDER HERE

    }
}
