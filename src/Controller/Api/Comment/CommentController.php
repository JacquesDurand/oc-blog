<?php

declare(strict_types=1);

namespace App\Controller\Api\Comment;

require_once __DIR__.'/../../../../vendor/autoload.php';

use App\Authentication\Role;
use App\Controller\Twig\AbstractController;
use App\HTTP\Request;
use App\Manager\CommentManager;
use App\Manager\PostManager;
use App\Manager\UserManager;
use App\Model\Comment;
use PDOException;

class CommentController extends AbstractController
{
    /** @var CommentManager */
    private CommentManager $commentManager;

    /** @var PostManager */
    private PostManager $postManager;

    /** @var UserManager  */
    private UserManager $userManager;

    public function __construct()
    {
        parent::__construct();
        $this->commentManager = new CommentManager();
        $this->postManager = new PostManager();
        $this->userManager = new UserManager();
    }

    public function addCommentOnPost(Request $request)
    {
        $slug = $request->requirements[0];

        switch ($request->method) {
            case 'GET':
                if ($post = $this->postManager->getPostBySlug($slug)) {
                    $this->generateCsrfToken($request);
                    echo $this->render('Front/Comment/form.html.twig', [
                        'post' => $post,
                        'token' => $_SESSION['csrf_token'],
                        'isAdmin' => isset($request->session['role']) && !empty($request->session['role']) && Role::ROLE_ADMIN <= $request->session['role']

                    ]);
                } else {
                    echo $this->render('Errors/404_resource.html.twig');
                }
                break;
            case 'POST':
                if (!$this->verifyCsrfToken($request)) {
                    echo $this->render('Errors/Csrf.html.twig');
                } else {
                    $this->cleanInput($request);
                    $comment = $this->hydrateCommentFromRequest($request);
                    try {
                        $this->commentManager->addCommentToPost($comment);
                        header('Location: http://localhost/posts/'.$slug);
                    } catch (PDOException $exception) {
                        var_dump($exception->getMessage());
                    }
                }
                break;
        }
    }

    private function hydrateCommentFromRequest(Request $request): Comment
    {
        $comment = new Comment();

        $comment->setContent($request->request['content']);
        $comment->setState(Comment::STATE_AWAITING_MODERATION);
        $comment->setAuthor($this->userManager->getUserById($request->session['userId']));
        $comment->setPost($this->postManager->getPostBySlug($request->requirements[0]));

        return $comment;
    }
}
