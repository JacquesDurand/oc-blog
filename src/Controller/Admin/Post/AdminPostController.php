<?php

declare(strict_types=1);

namespace App\Controller\Admin\Post;

use App\Controller\Twig\AbstractController;
use App\Exception\ResourceNotFoundException;
use App\HTTP\Request;
use App\Manager\CategoryManager;
use App\Manager\PostManager;
use App\Manager\UserManager;
use App\Model\Post;
use PDOException;

require_once __DIR__.'/../../../../vendor/autoload.php';


class AdminPostController extends AbstractController
{
    /** @var PostManager */
    private PostManager $postManager;

    /** @var UserManager  */
    private UserManager $userManager;

    /** @var CategoryManager */
    private CategoryManager $categoryManager;

    public function __construct()
    {
        parent::__construct();
        $this->postManager = new PostManager();
        $this->userManager = new UserManager();
        $this->categoryManager = new CategoryManager();
    }

    public function getAllPosts(Request $request)
    {
        $posts = $this->postManager->getAllPosts();
        print_r($this->render('Admin/Post/show.html.twig', [
                'posts' => $posts
            ]));
    }

    public function getPostById(Request $request)
    {
        $postId = (int) $request->requirements[0];
        print_r($this->render('Admin/Post/show_one.html.twig', [
            'post' => $this->postManager->getPostById($postId)
        ]));
    }

    public function addPost(Request $request)
    {
        switch ($request->method) {
            case 'GET':
                $this->generateCsrfToken($request);
                $csrfToken = $_SESSION['csrf_token'];
                print_r($this->render('Admin/Post/form.html.twig', [
                    'token' => $csrfToken,
                    'categories' => $this->categoryManager->getAllCategories(),
                    'authors' => $this->userManager->getAllAdminUsers()
                ]));
                break;
            case 'POST':
                if (!$this->verifyCsrfToken($request)) {
                    print_r($this->render('Errors/Csrf.html.twig'));
                } else {
                    $this->cleanInput($request);
                    $post = $this->hydratePost($request);
                    try {
                        $this->postManager->createPost($post);
                        header('Location: http://localhost/admin/posts');
                    } catch (PDOException $exception) {
                        var_dump($exception->getMessage());
                    }
                }
        }
    }

    public function removePost(Request $request)
    {
        $postId = (int) $request->requirements[0];
        try {
            $this->postManager->deletePost($postId);
            print_r($this->render('Admin/Post/show.html.twig'));
        } catch (ResourceNotFoundException $exception) {
            print_r($this->render('Errors/404_resource.html.twig'));
        }
    }

    public function updatePost(Request $request)
    {
        $postId = (int) $request->requirements[0];

        switch ($request->method) {
            case 'GET':
                if ($post = $this->postManager->getPostById($postId)) {
                    $this->generateCsrfToken($request);
                    $csrfToken = $_SESSION['csrf_token'];
                    print_r($this->render('Admin/Post/update.html.twig', [
                        'post' => $post,
                        'token' => $csrfToken ,
                        'categories' => $this->categoryManager->getAllCategories(),
                        'authors' => $this->userManager->getAllAdminUsers()
                    ]));
                } else {
                    print_r($this->render('Errors/404_resource.html.twig'));
                }
                break;
            case 'POST':
                if (!$this->verifyCsrfToken($request)) {
                    print_r($this->render('Errors/Csrf.html.twig'));
                } else {
                    $this->cleanInput($request);
                    $post = $this->hydratePost($request);
                    try {
                        $this->postManager->updatePost($postId, $post);
                        header('Location: http://localhost/admin/posts');
                    } catch (ResourceNotFoundException $exception) {
                        print_r($this->render('Errors/404_resource.html.twig'));
                    }
                }
        }
    }

    private function hydratePost(Request $request): Post
    {
        $post = new Post();
        $post->setTitle($request->request['title']);
        $post->setLede($request->request['lede']);
        $post->setContent($request->request['content']);
        $post->slugify();
        $post->setState((int)$request->request['state']);
        $post->setAuthor($this->userManager->getUserById((int)$request->request['author']));
        $post->setCategory($this->categoryManager->getCategoryById((int)$request->request['category']));

        return $post;
    }
}
