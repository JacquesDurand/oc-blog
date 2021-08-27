<?php

declare(strict_types=1);

namespace App\Controller\Api\Post;

use App\Authentication\Role;
use App\Controller\Twig\AbstractController;
use App\HTTP\Request;
use App\Manager\CategoryManager;
use App\Manager\CommentManager;
use App\Manager\PostManager;

require_once __DIR__.'/../../../../vendor/autoload.php';


class PostController extends AbstractController
{
    /** @var PostManager */
    private PostManager $postManager;

    /** @var CommentManager */
    private CommentManager $commentManager;

    /** @var CategoryManager */
    private CategoryManager $categoryManager;

    public function __construct()
    {
        parent::__construct();
        $this->postManager = new PostManager();
        $this->commentManager= new CommentManager();
        $this->categoryManager = new CategoryManager();
    }

    public function getAllPosts(Request $request)
    {
        $posts = $this->postManager->getAllPosts();
        print_r($this->render('Front/Post/show.html.twig', [
            'isConnected' => isset($request->session['userId']) && !empty($request->session['userId']),
            'isAdmin' => isset($request->session['role']) && !empty($request->session['role']) && Role::ROLE_ADMIN <= $request->session['role'],
            'posts' => $posts
        ]));
    }

    public function getAllPostsByCategory(Request $request)
    {
        $category = $request->requirements[1];
        if (null !== $category) {
            $posts = $this->postManager->getPostsByCategoryName($category);
            print_r($this->render('Front/Post/show.html.twig', [
                'isConnected' => isset($request->session['userId']) && !empty($request->session['userId']),
                'posts' => $posts,
                'category' => $this->categoryManager->getCategoryByName($category),
                'isAdmin' => isset($request->session['role']) && !empty($request->session['role']) && Role::ROLE_ADMIN <= $request->session['role']
            ]));
        } else {
            print_r($this->render('Errors/404_resource.html.twig'));
        }
    }

    public function getAllPostsByAuthor(Request $request)
    {
        $authorId = (int) $request->requirements[1];
        if (null !== $authorId) {
            $posts = $this->postManager->getPostsByAuthor($authorId);
            print_r($this->render('Front/Post/show.html.twig', [
                'isConnected' => isset($request->session['userId']) && !empty($request->session['userId']),
                'posts' => $posts,
                'isAdmin' => isset($request->session['role']) && !empty($request->session['role']) && Role::ROLE_ADMIN <= $request->session['role']
            ]));
        } else {
            print_r($this->render('Errors/404_resource.html.twig'));
        }
    }

    public function getSinglePost(Request $request)
    {
        $slug = $request->requirements[0];
        if (null !== $slug) {
            $post = $this->postManager->getPostBySlug($slug);
            if (null !== $post) {
                $comments = $this->commentManager->getCommentsForPost($post);
                print_r($this->render('Front/Post/show_one.html.twig', [
                    'isConnected' => isset($request->session['userId']) && !empty($request->session['userId']),
                    'post' => $post,
                    'comments' => $comments,
                    'isAdmin' => isset($request->session['role']) && !empty($request->session['role']) && Role::ROLE_ADMIN <= $request->session['role']
                ]));
            }
        } else {
            print_r($this->render('Errors/404_resource.html.twig'));
        }
    }
}
