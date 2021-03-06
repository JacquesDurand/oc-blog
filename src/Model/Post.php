<?php

declare(strict_types=1);

namespace App\Model;

use Cocur\Slugify\Slugify;
use DateTime;

class Post
{
    /** @var int */
    private int $id;

    /** @var string */
    private string $title;

    /** @var string */
    private string $lede;

    /** @var string */
    private string $content;

    /** @var string */
    private string $slug;

    /** @var int */
    private int $state;

    /** @var Category|null */
    private ?Category $category;

    /** @var User */
    private User $author;

    /** @var DateTime */
    private DateTime $createdAt;

    /** @var DateTime */
    private DateTime $updatedAt;

    public const STATE_REMOVED = 0;
    public const STATE_DRAFT = 1;
    public const STATE_PUBLISHED = 2;

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
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getLede(): string
    {
        return $this->lede;
    }

    /**
     * @param string $lede
     */
    public function setLede(string $lede): void
    {
        $this->lede = $lede;
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
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     */
    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    public function slugify(): void
    {
        $slugify = new Slugify();
        if (null !== $this->getTitle()) {
            $slug = $slugify->slugify($this->getTitle());
            $slug = substr($slug, 0, 50);
            $words = explode('-', $slug);
            array_pop($words);
            $slug = implode('-', $words);
            $this->setSlug($slug);
        }
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
     * @return Category|null
     */
    public function getCategory(): ?Category
    {
        return $this->category;
    }

    /**
     * @param Category $category
     */
    public function setCategory(Category $category): void
    {
        $this->category = $category;
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
