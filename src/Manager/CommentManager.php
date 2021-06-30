<?php

declare(strict_types=1);

namespace App\Manager;

require_once __DIR__.'/../../vendor/autoload.php';

use App\Exception\ResourceNotFoundException;
use App\HTTP\Request;
use App\Model\Comment;
use App\Traits\DbInstanceTrait;
use PDO;
use PDOException;

class CommentManager
{
    use DbInstanceTrait;

    /** @var UserManager */
    private UserManager $userManager;

    /** @var PostManager */
    private PostManager $postManager;

    public function __construct()
    {
        $this->connect();
        $this->postManager = new PostManager();
        $this->userManager = new UserManager();
    }

    public function getAllComments(): array
    {
        $req = $this->dbInstance->prepare('SELECT * FROM comment');
        $req->execute();
        return $this->hydrateCommentsWithFk($req->fetchAll());
    }

    public function getCommentById(int $id)
    {
        $req = $this->dbInstance->prepare('SELECT * FROM comment WHERE id=:id');
        $req->bindValue(':id', $id);
        $req->execute();
        if ($result = $req->fetch()) {
            return $this->hydrateCommentWithFk($result);
        } else {
            return false;
        }
    }

    /**
     * @param int $id
     * @param string $moderationReason
     * @throws ResourceNotFoundException
     */
    public function moderateComment(int $id, string $moderationReason)
    {
        if ($this->getCommentById($id)) {
            $req = $this->dbInstance->prepare(
                'UPDATE comment 
                   SET moderation_reason = :moderationReason, state = :state
                   WHERE id =:id'
            );
            $req->bindValue(':moderationReason', $moderationReason);
            $req->bindValue(':state', Comment::STATE_MODERATED);
            $req->bindValue(':id', $id);
            $req->execute();
        } else {
            throw new ResourceNotFoundException();
        }
    }

    /**
     * @param Comment $comment
     * @throws PDOException
     */
    public function addCommentToPost(Comment $comment)
    {
        $req = $this->dbInstance->prepare(
            'INSERT INTO comment(content, state, moderation_reason, author_id, post_id)
                VALUES (:content, :state, :moderationReason, :author, :post)'
        );
        $req->bindValue(':content', $comment->getContent());
        $req->bindValue(':state', $comment->getState());
        $req->bindValue(':moderationReason', $comment->getModerationReason());
        $req->bindValue(':author', $comment->getAuthor()->getId());
        $req->bindValue(':post', $comment->getPost()->getId());

        $req->execute();
    }

    public function updateComment(Comment $comment)
    {
        $req = $this->dbInstance->prepare(
            'UPDATE comment
                   SET content = :content,
                   state = :state,
                   moderation_reason = :moderationReason,
                   author_id = :author,
                   post_id = :post
                   WHERE id = :id'
        );
        $req->bindValue(':content', $comment->getContent());
        $req->bindValue(':state', $comment->getState());
        $req->bindValue(':moderationReason', $comment->getModerationReason());
        $req->bindValue(':author', $comment->getAuthor()->getId());
        $req->bindValue(':post', $comment->getPost()->getId());
        $req->bindValue(':id', $comment->getId());

        $req->execute();
    }

    /**
     * @param int $id
     * @throws ResourceNotFoundException
     */
    public function approveComment(int $id)
    {
        if ($this->getCommentById($id)) {
            $req = $this->dbInstance->prepare('UPDATE comment SET state = :state WHERE id = :id');
            $req->bindValue(':state', Comment::STATE_VALIDATED);
            $req->bindValue('id', $id);
            $req->execute();
        } else {
            throw new ResourceNotFoundException();
        }
    }

    private function hydrateCommentsWithFk(array $dbComments): array
    {
        $comments = [];

        foreach ($dbComments as $dbComment) {
            $comments[] = $this->hydrateCommentWithFk($dbComment);
        }
        return $comments;
    }

    private function hydrateCommentWithFk(array $dbComment): Comment
    {
        $comment = new Comment();

        $comment->setId($dbComment['id']);
        $comment->setState($dbComment['state']);
        $comment->setContent($dbComment['content']);
        $comment->setModerationReason($dbComment['moderation_reason']);
        $comment->setCreatedAt(new \DateTime($dbComment['created_at']));
        $comment->setUpdatedAt(new \DateTime($dbComment['updated_at']));
        $comment->setAuthor($this->userManager->getUserById($dbComment['author_id']));
        $comment->setPost($this->postManager->getPostById($dbComment['post_id']));

        return $comment;
    }
}
