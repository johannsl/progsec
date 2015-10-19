<?php

namespace tdt4237\webapp\validation;

use tdt4237\webapp\models\Post;

class PostValidation extends AbstractCsrfProtectedForm {


    public function __construct($author, $title, $content, $token) {
        parent::__construct($token);
        return $this->validate($author, $title, $content);
    }

    public function isGoodToGo()
    {
        return \count($this->validationErrors) ===0;
    }

    public function getValidationErrors()
    {
    return $this->validationErrors;
    }

    public function validate($author, $title, $content)
    {
        if ($author == null) {
            $this->validationErrors[] = "Author needed";

        }
        if ($title == null || strlen($title) < 1) {
            $this->validationErrors[] = "Title needed";
        }

        if ($content == null || strlen($content) < 1) {
            $this->validationErrors[] = "Text needed";
        }

        return $this->validationErrors;
    }


}
