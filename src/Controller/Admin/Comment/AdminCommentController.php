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

    public function getAllComments(Request $request)
    {
        $comments = $this->commentManager->getAllComments();
        echo $this->render('Admin/Comment/show.html.twig', [
            'comments' => $comments
        ]);
    }

    public function getCommentById(Request $request)
    {
        $id = (int) $request->requirements[0];
        if ($comment = $this->commentManager->getCommentById($id)) {
            echo $this->render('Admin/Comment/show_one.html.twig', [
                'comment' => $comment
            ]);
        } else {
            echo $this->render('Errors/404_resource.html.twig');
        }
    }

    public function addComment(Request $request)
    {
        $id = (int) $request->requirements[0];
        switch ($request->method) {
            case 'GET':
                if ($post = $this->postManager->getPostById($id)) {
                    $this->generateCsrfToken($request);
                    echo $this->render('Admin/Comment/form.html.twig', [
                       'post' => $post,
                       'token' => $_SESSION['csrf_token']
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
                        header('Location: http://localhost/admin/comments');
                    } catch (PDOException $exception) {
                        var_dump($exception->getMessage());
                    }
                }
                break;
        }
    }

    public function updateComment(Request $request)
    {
        $id = (int) $request->requirements[0];
        switch ($request->method) {
            case 'GET':
                if ($comment = $this->commentManager->getCommentById($id)) {
                    $this->generateCsrfToken($request);
                    echo $this->render('Admin/Comment/update.html.twig', [
                       'comment' => $comment,
                       'token' => $_SESSION['csrf_token']
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
                    $comment->setId($id);
                    try {
                        $this->commentManager->updateComment($comment);
                        header('Location: http://localhost/admin/comments');
                    } catch (ResourceNotFoundException $exception) {
                        echo $this->render('Errors/404_resource.html.twig');
                    }
                }
        }
    }

    public function moderate(Request $request)
    {
        $id = (int) $request->requirements[0];
        switch ($request->method) {
            case 'GET':
                if ($comment = $this->commentManager->getCommentById($id)) {
                    $this->generateCsrfToken($request);
                    echo $this->render('Admin/Comment/moderate.html.twig', [
                        'comment' => $comment,
                        'token' => $_SESSION['csrf_token']
                    ]);
                } else {
                    echo $this->render('Errors/404_resource.html.twig');
                }
                break;
            case 'POST':
                if (!$this->verifyCsrfToken($request)) {
                    echo $this->render('Errors/Csrf.html.twig');
                } else {
                    try {
                        $this->cleanInput($request);
                        $this->commentManager->moderateComment($id, $request->request['moderationReason']);
                        header('Location: http://localhost/admin/comments');
                    } catch (ResourceNotFoundException $exception) {
                        echo $this->render('Errors/404_resource.html.twig');
                    }
                }
        }
    }

    public function approve(Request $request)
    {
        $id = (int) $request->requirements[0];
        try {
            $this->commentManager->approveComment($id);
            header('Location: http://localhost/admin/comments');
        } catch (ResourceNotFoundException $exception) {
            echo $this->render('Errors/404_resource.html.twig');
        }
    }

    private function hydrateCommentFromRequest(Request $request): Comment
    {
        $comment = new Comment();

        $comment->setContent($request->request['content']);
        $comment->setModerationReason($request->request['moderationReason']);
        $comment->setState(Comment::STATE_VALIDATED);
        $comment->setAuthor($this->userManager->getUserById((int) $request->request['author']));
        $comment->setPost($this->postManager->getPostById((int) $request->requirements[0]));

        return $comment;
    }
}
