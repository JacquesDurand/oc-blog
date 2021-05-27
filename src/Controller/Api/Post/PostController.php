<?php

declare(strict_types=1);

namespace App\Controller\Api\Post;

use App\HTTP\Request;
use App\Manager\PostManager;

require_once __DIR__.'/../../../../vendor/autoload.php';


class PostController
{
    /** @var PostManager */
    private $postManager;

    public function construct()
    {
        $this->postManager = new PostManager();
    }

    public function getAllPosts(Request $request)
    {
    }

    public function getAllPostsByCategory(Request $request)
    {
    }

    public function getAllPostsByAuthor(Request $request)
    {
    }

    public function getSinglePost(Request $request)
    {
    }
}
