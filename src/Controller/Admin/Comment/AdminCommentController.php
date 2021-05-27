<?php

declare(strict_types=1);

namespace App\Controller\Admin\Comment;

require_once __DIR__.'/../../../../vendor/autoload.php';

use App\HTTP\Request;
use App\Manager\CommentManager;

class AdminCommentController
{
    /** @var CommentManager */
    private $commentManager;

    public function __construct()
    {
        $this->commentManager = new CommentManager();
    }

    public function getAllComments(Request $request)
    {
    }

    public function getCommentById(Request $request)
    {
    }

    public function moderate(Request $request)
    {
    }

    public function approve(Request $request)
    {
    }
}
