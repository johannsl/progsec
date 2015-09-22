<?php

namespace tdt4237\webapp\controllers;

use tdt4237\webapp\Auth;
use tdt4237\webapp\models\User;

class AdminController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

	//this seams OK
    public function index()
    {
        if ($this->auth->guest()) {
            $this->app->flash('info', "You must be logged in to view the admin page.");
            $this->app->redirect('/');
        }

        if (! $this->auth->isAdmin()) {
            $this->app->flash('info', "You must be administrator to view the admin page.");
            $this->app->redirect('/');
        }

        $variables = [
            'users' => $this->userRepository->all(),
            'posts' => $this->postRepository->all()
        ];
        $this->render('admin.twig', $variables);
    }

	// there should be check if $this->auth->isAdmin() before deleting
	// G21_0009
    public function delete($username)
    {
        if ($this->userRepository->deleteByUsername($username) === 1) {
            $this->app->flash('info', "Sucessfully deleted '$username'");
            $this->app->redirect('/admin');
            return;
        }
        
        $this->app->flash('info', "An error ocurred. Unable to delete user '$username'.");
        $this->app->redirect('/admin');
    }

	// there should be check if $this->auth->isAdmin() before deleting (G21_0009)
    public function deletePost($postId)
    {
        if ($this->postRepository->deleteByPostid($postId) === 1) {
            $this->app->flash('info', "Sucessfully deleted '$postId'");
            $this->app->redirect('/admin');
            return;
        }

        $this->app->flash('info', "An error ocurred. Unable to delete user '$username'.");
        $this->app->redirect('/admin');
    }
}
