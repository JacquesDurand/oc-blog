<?php

declare(strict_types=1);

namespace App\Controller\Api\Post;

use App\Controller\Twig\AbstractController;
use App\HTTP\Request;
use App\Manager\CommentManager;
use App\Manager\PostManager;

require_once __DIR__.'/../../../../vendor/autoload.php';


class PostController extends AbstractController
{
    /** @var PostManager */
    private PostManager $postManager;

    /** @var CommentManager */
    private CommentManager $commentManager;

    public function __construct()
    {
        parent::__construct();
        $this->postManager = new PostManager();
        $this->commentManager= new CommentManager();
    }

    public function getAllPosts(Request $request)
    {
        $posts = $this->postManager->getAllPosts();
        echo $this->render('Front/Post/show.html.twig', [
            'posts' => $posts
        ]);
    }

    public function getAllPostsByCategory(Request $request)
    {
        $category = $request->requirements[1];
        if (null !== $category) {
            $posts = $this->postManager->getPostsByCategoryName($category);
            echo $this->render('Front/Post/show.html.twig', [
                'posts' => $posts
            ]);
        } else {
            echo $this->render('Errors/404_resource.html.twig');
        }
    }

    public function getAllPostsByAuthor(Request $request)
    {
        $authorId = (int) $request->requirements[1];
        if (null !== $authorId) {
            $posts = $this->postManager->getPostsByAuthor($authorId);
            echo $this->render('Front/Post/show.html.twig', [
                'posts' => $posts
            ]);
        } else {
            echo $this->render('Errors/404_resource.html.twig');
        }
    }

    public function getSinglePost(Request $request)
    {
        $slug = $request->requirements[0];
        if (null !== $slug) {
            $post = $this->postManager->getPostBySlug($slug);
            if (null !== $post) {
                $comments = $this->commentManager->getCommentsForPost($post);
                echo $this->render('Front/Post/show_one.html.twig', [
                    'post' => $post,
                    'comments' => $comments
                ]);
            }
        } else {
            echo $this->render('Errors/404_resource.html.twig');
        }
    }
}
