<?php

declare(strict_types=1);

namespace App\Manager;

require_once __DIR__.'/../../vendor/autoload.php';

use App\Exception\ResourceNotFoundException;
use App\Traits\DbInstanceTrait;
use PDO;

class CategoryManager
{
    use DbInstanceTrait;

    public function __construct()
    {
        $this->connect();
    }

    public function getAllCategories(): array
    {
        $req = $this->dbInstance->prepare('SELECT * FROM category');
        $req->execute();
        $req->setFetchMode(PDO::FETCH_CLASS, 'App\Model\Category');
        return $req->fetchAll();
    }

    public function getCategoryById(int $categoryId)
    {
        $req = $this->dbInstance->prepare('SELECT * FROM category WHERE id=:id');
        $req->bindValue(':id', $categoryId);
        $req->execute();
        $req->setFetchMode(PDO::FETCH_CLASS, 'App\Model\Category');
        return $req->fetch();
    }

    public function getCategoryByName(string $categoryName)
    {
        $req = $this->dbInstance->prepare('SELECT * FROM category WHERE name=:name');
        $req->bindValue(':name', $categoryName);
        $req->execute();
        $req->setFetchMode(PDO::FETCH_CLASS, 'App\Model\Category');
        return $req->fetch();
    }

    public function createCategory(string $name): void
    {
        $req = $this->dbInstance->prepare('INSERT INTO category(name) VALUES (:name)');
        $req->bindValue(':name', $name);
        $req->execute();
    }

    /**
     * @throws ResourceNotFoundException
     */
    public function deleteCategory(int $categoryId): void
    {
        $category = $this->getCategoryById($categoryId);
        if ($category) {
            $req = $this->dbInstance->prepare('DELETE FROM category WHERE id=:id');
            $req->bindValue(':id', $categoryId);
            $req->execute();
        } else {
            throw new ResourceNotFoundException();
        }
    }

    /**
     * @throws ResourceNotFoundException
     */
    public function updateCategory(int $categoryId, string $name): void
    {
        if ($category = $this->getCategoryById($categoryId)) {
            $req = $this->dbInstance->prepare('UPDATE category SET name=:name WHERE id=:id');
            $req->bindValue(':name', $name);
            $req->bindValue(':id', $categoryId);
            $req->execute();
        } else {
            throw new ResourceNotFoundException();
        }
    }
}
