<?php

namespace tdt4237\webapp\validation;

use tdt4237\webapp\models\Post;

class CommentValidation extends AbstractCsrfProtectedForm {


    public function __construct($content, $postid, $token) {
        parent::__construct($token);
        return $this->validate($content, $postid);
    }

    public function isGoodToGo()
    {
        return \count($this->validationErrors) ===0;
    }

    public function getValidationErrors()
    {
    return $this->validationErrors;
    }

    public function validate($content, $postid)
    {
        if ($content == null || strlen($content) < 1) {
            $this->validationErrors[] = "Text needed for comment";
        }

        //check that post exists
        $app = \Slim\Slim::getInstance();
        if($app->postRepository->find($postid) === false) {
            $this->validationErrors[] = "Post to which comment should be appended does not exist";
        }

        return $this->validationErrors;
    }


}
