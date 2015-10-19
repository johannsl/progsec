<?php

namespace tdt4237\webapp\controllers;

class Controller
{
    protected $app;
    
    protected $userRepository;
    protected $auth;
    protected $postRepository;

    public function __construct()
    {
        $this->app = \Slim\Slim::getInstance();
        $this->userRepository = $this->app->userRepository;
        $this->postRepository = $this->app->postRepository;
        $this->postRepository = $this->app->postRepository;
        $this->commentRepository = $this->app->commentRepository;
        $this->auth = $this->app->auth;
        $this->hash = $this->app->hash;

        if(!isset($_SESSION['csrftoken'])) {
           $_SESSION['csrftoken'] = $this->auth->generateCsrfToken();
        }
    }

    protected function render($template, $variables = [])
    {
        $variables['csrftoken'] = $_SESSION['csrftoken'];

        if ($this->auth->check()) {
            $variables['isLoggedIn'] = true;
            $variables['isAdmin'] = $this->auth->isAdmin();
            $variables['loggedInUsername'] = $_SESSION['user'];
            
        } 

        print $this->app->render($template, $variables);
    }
}
