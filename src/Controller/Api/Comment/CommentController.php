<?php

declare(strict_types=1);

namespace App\Controller\Api\Comment;

require_once __DIR__.'/../../../../vendor/autoload.php';

use App\HTTP\Request;
use App\Manager\CommentManager;

class CommentController
{
    /** @var CommentManager */
    private $commentManager;

    public function __construct()
    {
        $this->commentManager = new CommentManager();
    }

    public function addCommentOnPost(Request $request)
    {
    }
}
