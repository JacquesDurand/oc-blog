<?php

declare(strict_types=1);

namespace App\Manager;

require_once __DIR__.'/../../vendor/autoload.php';

use App\Exception\ResourceNotFoundException;
use App\HTTP\Request;
use App\Model\Comment;
use App\Model\Post;
use App\Model\User;
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

    /**
     * Returns all Comments
     * @return array
     */
    public function getAllComments(): array
    {
        $req = $this->dbInstance->prepare('SELECT * FROM comment');
        $req->execute();
        return $this->hydrateCommentsWithFk($req->fetchAll());
    }

    /**
     * Return a single Comment (by Id)
     * @param int $commentId
     * @return Comment|false
     */
    public function getCommentById(int $commentId)
    {
        $req = $this->dbInstance->prepare('SELECT * FROM comment WHERE id=:id');
        $req->bindValue(':id', $commentId);
        $req->execute();
        if ($result = $req->fetch()) {
            return $this->hydrateCommentWithFk($result);
        } else {
            return false;
        }
    }

    /**
     * Sets a Comment's state to moderated and adds a reason
     * @param int $commentId
     * @param string $moderationReason
     * @throws ResourceNotFoundException
     */
    public function moderateComment(int $commentId, string $moderationReason)
    {
        if ($this->getCommentById($commentId)) {
            $req = $this->dbInstance->prepare(
                'UPDATE comment 
                   SET moderation_reason = :moderationReason, state = :state
                   WHERE id =:id'
            );
            $req->bindValue(':moderationReason', $moderationReason);
            $req->bindValue(':state', Comment::STATE_MODERATED);
            $req->bindValue(':id', $commentId);
            $req->execute();
        } else {
            throw new ResourceNotFoundException();
        }
    }

    /**
     * Creates a Comment for a Post (by PostId)
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

    /**
     * Updates a Comment (by Id)
     * @param Comment $comment
     */
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
     * Sets a Comment's state to validated
     * @param int $commentId
     * @throws ResourceNotFoundException
     */
    public function approveComment(int $commentId)
    {
        if ($this->getCommentById($commentId)) {
            $req = $this->dbInstance->prepare('UPDATE comment SET state = :state WHERE id = :id');
            $req->bindValue(':state', Comment::STATE_VALIDATED);
            $req->bindValue('id', $commentId);
            $req->execute();
        } else {
            throw new ResourceNotFoundException();
        }
    }

    /**
     * Gets all Comments for a Post
     * @param Post $post
     * @return array
     * @throws \Exception
     */
    public function getCommentsForPost(Post $post): array
    {
        $req = $this->dbInstance->prepare('SELECT * FROM comment WHERE post_id =:id AND state = :state');
        $req->bindValue(':id', $post->getId());
        $req->bindValue(':state', Comment::STATE_VALIDATED);
        $req->execute();
        return $this->hydrateCommentsWithFk($req->fetchAll());
    }

    /**
     * Gets all Comments by a User
     * @param User $user
     * @return array
     * @throws \Exception
     */
    public function getCommentsForAuthor(User $user): array
    {
        $req = $this->dbInstance->prepare('SELECT * FROM comment WHERE author_id =:id');
        $req->bindValue(':id', $user->getId());
        $req->execute();
        return $this->hydrateCommentsWithFk($req->fetchAll());
    }

    /**
     * Creates Comments from db resources
     * @param array $dbComments
     * @return array
     * @throws \Exception
     */
    private function hydrateCommentsWithFk(array $dbComments): array
    {
        $comments = [];

        foreach ($dbComments as $dbComment) {
            $comments[] = $this->hydrateCommentWithFk($dbComment);
        }
        return $comments;
    }

    /**
     * Creates a single Comment from db resources
     * @param array $dbComment
     * @return Comment
     * @throws \Exception
     */
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
