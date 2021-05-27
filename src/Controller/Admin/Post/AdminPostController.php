<?php

declare(strict_types=1);

namespace App\Controller\Admin\Post;

use App\HTTP\Request;
use App\Manager\PostManager;

require_once __DIR__.'/../../../../vendor/autoload.php';


class AdminPostController
{
    /** @var PostManager */
    private $postManager;

    public function __construct()
    {
        $this->postManager = new PostManager();
    }

    public function getAllPosts(Request $request)
    {
    }

    public function getPostById(Request $request)
    {
    }

    public function addPost(Request $request)
    {
    }

    public function removePost(Request $request)
    {
    }

    public function updatePost(Request $request)
    {
    }
}
