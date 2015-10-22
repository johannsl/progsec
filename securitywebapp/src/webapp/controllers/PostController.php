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
        if ($this->auth->guest()) {
            $this->app->flash("info", "You must be logged in to do that");
            $this->app->redirect("/login");
        }else{
            $posts = $this->postRepository->all();
            $posts->sortByDate();
            $username = $_SESSION['user'];
            $user = $this->userRepository->findByUser($username);
            $this->render('posts.twig', ['posts' => $posts, 'user' => $user]);
        }
    }

    public function show($postId)
    {
        $user = $this->userRepository->findByUser($_SESSION['user']);
        $post = $this->postRepository->find($postId);
        if ($this->auth->guest() || $user == false) {
            $this->app->flash("info", "You must be logged in to do that");
            $this->app->redirect("/login");
        } 
        elseif (($this->postRepository->find($postId)) && ($user->isDoctor() == true)) {
            if ($post->getPay() == 0) {
                $this->app->flash("info", "Doctors cannot view unfunded posts");
                $this->app->redirect("/posts");
            } else {
                if ($this->postRepository->acquireLock($postId, $_SESSION['user'])) {
                    $comments = $this->commentRepository->findByPostId($postId);
                    $request = $this->app->request;

                    $this->render('showpost.twig', [
                        'post' => $post,
                        'comments' => $comments,
                        ]);
                } else {
                    $this->app->flash("info", "There is already a doctor working on this post");
                    $this->app->redirect("/posts");
                }
            }
        } else {
            $comments = $this->commentRepository->findByPostId($postId);
            $request = $this->app->request;

            $this->render('showpost.twig', [
                'post' => $post,
                'comments' => $comments,
            ]);
        }

        // What is this I dont even.
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
                
                $author_name = $comment->getAuthor();
                $author = $this->userRepository->findByUser($author_name);

                if ($author->isDoctor() == true)
                {
                    $post = $this->postRepository->find($postId);
                    if ($post->getAnswerByDoctor() == 0)
                    {
                        $post->setAnswerByDoctor(1);
                        $this->postRepository->answeredByDoctor($postId);
					    $this->userRepository->payMoney($post->getAuthor(), $author_name, 10); 
                    }
                }

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
            $user = $this->userRepository->findByUser($username);
            $this->render('createpost.twig', ['user' => $user]);
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
        } else if ($this->userRepository->findByUser($_SESSION['user'])->isDoctor() == true) {
            $this->app->flash("info", "Doctors cannot create posts");
            $this->app->redirect("/posts");
        } else {
            $request = $this->app->request;
            $title = $request->post('title');
            $content = $request->post('content');
            $pay = $request->post('pay');
            $author = $_SESSION['user'];
            $date = date("dmY");
            $validation = new PostValidation($author, $title, $content, $request->post('csrftoken'));
            if ($validation->isGoodToGo()) {
                $post = new Post();
                $post->setAuthor($author);
                $post->setTitle($title);
                $post->setContent($content);
                $post->setDate($date);
                $post->setPay($pay);
                $savedPost = $this->postRepository->save($post);
                $this->app->flash('info', 'Post succesfully posted');
                $this->app->redirect('/posts/' . $savedPost);
            }
        }
            // Does this ever occur?
            $this->app->flashNow('error', join("\n", $validation->getValidationErrors()));
            $username = $_SESSION['user'];
            $user = $this->userRepository->findByUser($username);
            $this->render('createpost.twig', ['user' => $user]);
    }
}
