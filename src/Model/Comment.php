<?php

declare(strict_types=1);

namespace App\Model;

use DateTime;

class Comment
{
    /** @var int */
    private int $id;

    /** @var string */
    private string $content;

    /** @var int */
    private int $state;

    /** @var string|null */
    private ?string $moderationReason;

    /** @var User */
    private User $author;

    /** @var Post */
    private Post $post;

    /** @var DateTime */
    private DateTime $createdAt;

    /** @var DateTime */
    private DateTime $updatedAt;

    public const STATE_MODERATED = 0;
    public const STATE_AWAITING_MODERATION = 1;
    public const STATE_VALIDATED = 2;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    /**
     * @return int
     */
    public function getState(): int
    {
        return $this->state;
    }

    /**
     * @param int $state
     */
    public function setState(int $state): void
    {
        $this->state = $state;
    }

    /**
     * @return string
     */
    public function getModerationReason(): ?string
    {
        return $this->moderationReason;
    }

    /**
     * @param ?string $moderationReason
     */
    public function setModerationReason(?string $moderationReason): void
    {
        $this->moderationReason = $moderationReason;
    }

    /**
     * @return User
     */
    public function getAuthor(): User
    {
        return $this->author;
    }

    /**
     * @param User $author
     */
    public function setAuthor(User $author): void
    {
        $this->author = $author;
    }

    /**
     * @return Post
     */
    public function getPost(): Post
    {
        return $this->post;
    }

    /**
     * @param Post $post
     */
    public function setPost(Post $post): void
    {
        $this->post = $post;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param DateTime $createdAt
     */
    public function setCreatedAt(DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return DateTime
     */
    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param DateTime $updatedAt
     */
    public function setUpdatedAt(DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }
}
