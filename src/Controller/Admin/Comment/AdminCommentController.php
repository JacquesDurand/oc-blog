<?php

declare(strict_types=1);

namespace App\Controller\Admin\Comment;

require_once __DIR__.'/../../../../vendor/autoload.php';

use App\Controller\Twig\AbstractController;
use App\Exception\ResourceNotFoundException;
use App\HTTP\Request;
use App\Manager\CommentManager;
use App\Manager\PostManager;
use App\Manager\UserManager;
use App\Model\Comment;
use PDOException;

class AdminCommentController extends AbstractController
{
    /** @var CommentManager */
    private CommentManager $commentManager;

    /** @var PostManager */
    private PostManager $postManager;

    /** @var UserManager */
    private UserManager $userManager;

    public function __construct()
    {
        parent::__construct();
        $this->commentManager = new CommentManager();
        $this->postManager = new PostManager();
        $this->userManager = new UserManager();
    }

    /**
     * Renders all Comments admin-side
     * @param Request $request
     */
    public function getAllComments(Request $request)
    {
        $comments = $this->commentManager->getAllComments();
        print_r($this->render('Admin/Comment/show.html.twig', [
            'comments' => $comments,
            'posts' => $this->postManager->getAllPosts()
        ]));
    }

    /**
     * Renders a single Comment (by Id) admin-side
     * @param Request $request
     */
    public function getCommentById(Request $request)
    {
        $commentId = (int) $request->requirements[0];
        if ($comment = $this->commentManager->getCommentById($commentId)) {
            print_r($this->render('Admin/Comment/show_one.html.twig', [
                'comment' => $comment
            ]));
        } else {
            print_r($this->render('Errors/404_resource.html.twig'));
        }
    }

    /**
     * Adds a single Comment to aPost (by PostId) admin-side
     * @param Request $request
     */
    public function addComment(Request $request)
    {
        $postId = (int) $request->requirements[0];
        switch ($request->method) {
            case 'GET':
                if ($post = $this->postManager->getPostById($postId)) {
                    $this->generateCsrfToken($request);
                    $csrfToken = $_SESSION['csrf_token'];
                    print_r($this->render('Admin/Comment/form.html.twig', [
                       'post' => $post,
                       'token' => $csrfToken,

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
                    $comment = $this->hydrateCommentFromRequest($request);
                    try {
                        $this->commentManager->addCommentToPost($comment);
                        header('Location: http://localhost/admin/comments');
                    } catch (PDOException $exception) {
                        print_r($this->render('Errors/500.html.twig'));
                    }
                }
                break;
            default:
                print_r($this->render('Errors/404.html.twig'));

        }
    }

    /**
     * Updates a single Comment (by Id) admin-side
     * @param Request $request
     */
    public function updateComment(Request $request)
    {
        $commentId = (int) $request->requirements[0];
        $comment = $this->commentManager->getCommentById($commentId);
        switch ($request->method) {
            case 'GET':
                if ($comment) {
                    $this->generateCsrfToken($request);
                    $csrfToken = $_SESSION['csrf_token'];
                    print_r($this->render('Admin/Comment/update.html.twig', [
                       'comment' => $comment,
                       'token' => $csrfToken,
                        'post' => $comment->getPost()
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
                    $comment->setContent($request->request['content']);
                    try {
                        $this->commentManager->updateComment($comment);
                        header('Location: http://localhost/admin/comments');
                    } catch (ResourceNotFoundException $exception) {
                        print_r($this->render('Errors/404_resource.html.twig'));
                    }
                }
                break;
            default:
                print_r($this->render('Errors/404.html.twig'));
        }
    }

    /**
     * Moderates a single Comment (by Id) admin-side
     * @param Request $request
     */
    public function moderate(Request $request)
    {
        $commentId = (int) $request->requirements[0];
        switch ($request->method) {
            case 'GET':
                if ($comment = $this->commentManager->getCommentById($commentId)) {
                    $this->generateCsrfToken($request);
                    $csrfToken = $_SESSION['csrf_token'];
                    print_r($this->render('Admin/Comment/moderate.html.twig', [
                        'comment' => $comment,
                        'token' => $csrfToken
                    ]));
                } else {
                    print_r($this->render('Errors/404_resource.html.twig'));
                }
                break;
            case 'POST':
                if (!$this->verifyCsrfToken($request)) {
                    print_r($this->render('Errors/Csrf.html.twig'));
                } else {
                    try {
                        $this->cleanInput($request);
                        $this->commentManager->moderateComment($commentId, $request->request['moderationReason']);
                        header('Location: http://localhost/admin/comments');
                    } catch (ResourceNotFoundException $exception) {
                        print_r($this->render('Errors/404_resource.html.twig'));
                    }
                }
                break;
            default:
                print_r($this->render('Errors/404.html.twig'));
        }
    }

    /**
     * Approves a single Comment (by Id) admin-side
     * @param Request $request
     */
    public function approve(Request $request)
    {
        $commentId = (int) $request->requirements[0];
        try {
            $this->commentManager->approveComment($commentId);
            header('Location: http://localhost/admin/comments');
        } catch (ResourceNotFoundException $exception) {
            print_r($this->render('Errors/404_resource.html.twig'));
        }
    }

    /**
     * Creates a Comment from the Request parameters
     * @param Request $request
     * @return Comment
     */
    private function hydrateCommentFromRequest(Request $request): Comment
    {
        $comment = new Comment();

        $comment->setContent($request->request['content']);
        $comment->setModerationReason($request->request['moderationReason'] ?: '');
        $comment->setState(Comment::STATE_VALIDATED);
        $comment->setAuthor($this->userManager->getUserById((int) $request->session['userId']));
        $comment->setPost($this->postManager->getPostById((int) $request->requirements[0]));

        return $comment;
    }
}
