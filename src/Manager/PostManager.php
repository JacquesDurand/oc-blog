<?php

declare(strict_types=1);

namespace App\Manager;

require_once __DIR__.'/../../vendor/autoload.php';

use App\Exception\ResourceNotFoundException;
use App\Model\Category;
use App\Model\Post;
use App\Traits\DbInstanceTrait;
use PDO;
use PDOException;

class PostManager
{
    use DbInstanceTrait;

    /** @var CategoryManager */
    private CategoryManager $categoryManager;

    /** @var UserManager */
    private UserManager $userManager;

    public function __construct()
    {
        $this->connect();
        $this->categoryManager = new CategoryManager();
        $this->userManager = new UserManager();
    }

    /**
     * Gets all Posts
     * @return array
     * @throws \Exception
     */
    public function getAllPosts(): array
    {
        $req = $this->dbInstance->prepare('SELECT * FROM post');
        $req->execute();
        return $this->hydratePostsWithFK($req->fetchAll());
    }

    /**
     * Gets a single Post (by Id)
     * @param int $postId
     * @return Post|false
     * @throws \Exception
     */
    public function getPostById(int $postId)
    {
        $req = $this->dbInstance->prepare('SELECT * FROM post WHERE id=:id');
        $req->bindValue(':id', $postId);
        $req->execute();
        if ($result = $req->fetch()) {
            return $this->hydratePostWithFk($result);
        } else {
            return false;
        }
    }

    /**
     * Gets a single Post (by slug)
     * @param string $slug
     * @return Post|false
     * @throws \Exception
     */
    public function getPostBySlug(string $slug)
    {
        $req = $this->dbInstance->prepare('SELECT * FROM post WHERE slug=:slug');
        $req->bindValue(':slug', $slug);
        $req->execute();
        if ($result = $req->fetch()) {
            return $this->hydratePostWithFk($result);
        } else {
            return false;
        }
    }

    /**
     * Gets every Posts for a Category
     * @param string $categoryName
     * @return array
     * @throws \Exception
     */
    public function getPostsByCategoryName(string $categoryName): array
    {
        $req = $this->dbInstance->prepare(
            'SELECT * FROM post p INNER JOIN category c ON p.category_id = c.id WHERE c.name = :name'
        );
        $req->bindValue(':name', $categoryName);
        $req->execute();
        return $this->hydratePostsWithFk($req->fetchAll());
    }

    /**
     * Gets every Posts created by a User
     * @param int $authorId
     * @return array
     * @throws \Exception
     */
    public function getPostsByAuthor(int $authorId): array
    {
        $req = $this->dbInstance->prepare(
            'SELECT * FROM post WHERE author_id = :id '
        );
        $req->bindValue(':id', $authorId);
        $req->execute();
        return $this->hydratePostsWithFk($req->fetchAll());
    }

    /**
     * Creates a new Post
     * @param Post $post
     * @throws PDOException
     */
    public function createPost(Post $post)
    {
        $req = $this->dbInstance->prepare(
            'INSERT INTO post(title, lede, content, slug, state, category_id, author_id) 
                   VALUES (:title, :lede, :content, :slug, :state, :categoryId, :authorId)'
        );
        $req->bindValue(':title', $post->getTitle());
        $req->bindValue(':lede', $post->getLede());
        $req->bindValue(':content', $post->getContent());
        $req->bindValue(':slug', $post->getSlug());
        $req->bindValue(':state', $post->getState());
        $req->bindValue(':categoryId', $post->getCategory()->getId());
        $req->bindValue(':authorId', $post->getAuthor()->getId());
        $req->execute();
    }

    /**
     * Deletes a Post (by Id)
     * @param int $postId
     * @throws ResourceNotFoundException
     */
    public function deletePost(int $postId)
    {
        if ($this->getPostById($postId)) {
            $req = $this->dbInstance->prepare('DELETE FROM post WHERE id=:id');
            $req->bindValue(':id', $postId);
            $req->execute();
        } else {
            throw new ResourceNotFoundException();
        }
    }

    /**
     * Updates a Post (by Id)
     * @param int $postId
     * @param Post $post
     * @throws ResourceNotFoundException
     */
    public function updatePost(int $postId, Post $post)
    {
        if ($this->getPostById($postId)) {
            $req = $this->dbInstance->prepare(
                'UPDATE post 
                       SET title=:title, 
                           lede=:lede, 
                           content=:content,
                           slug=:slug,
                           state=:state,
                           category_id=:categoryId,
                           author_id=:authorId
                     WHERE id=:id'
            );
            $req->bindValue(':title', $post->getTitle());
            $req->bindValue(':lede', $post->getLede());
            $req->bindValue(':content', $post->getContent());
            $req->bindValue(':slug', $post->getSlug());
            $req->bindValue(':state', $post->getState());
            $req->bindValue(':categoryId', $post->getCategory()->getId());
            $req->bindValue(':authorId', $post->getAuthor()->getId());
            $req->bindValue(':id', $postId);
            $req->execute();
        } else {
            throw new ResourceNotFoundException();
        }
    }

    /**
     * Creates Posts from db resources
     * @param array $dbPosts
     * @return array
     * @throws \Exception
     */
    private function hydratePostsWithFK(array $dbPosts): array
    {
        $posts = [];
        foreach ($dbPosts as $dbPost) {
            $posts[] = $this->hydratePostWithFk($dbPost);
        }

        return $posts;
    }

    /**
     * Creates a Post from db resources
     * @param array $dbPost
     * @return Post
     * @throws \Exception
     */
    private function hydratePostWithFk(array $dbPost): Post
    {
        $post = new Post();
        $post->setId($dbPost['id']);
        $post->setTitle($dbPost['title']);
        $post->setLede($dbPost['lede']);
        $post->setContent(stripslashes($dbPost['content']));
        $post->setSlug($dbPost['slug']);
        $post->setState($dbPost['state']);
        if (null === $dbPost['category_id']) {
            $category = new Category();
            $category->setName('Cat??gorie supprim??e');
            $post->setCategory($category);
        } else {
            $post->setCategory($this->categoryManager->getCategoryById($dbPost['category_id']));
        }
        $post->setAuthor($this->userManager->getUserById($dbPost['author_id']));
        $post->setCreatedAt(new \DateTime($dbPost['created_at']));
        $post->setCreatedAt(new \DateTime($dbPost['updated_at']));

        return $post;
    }
}
