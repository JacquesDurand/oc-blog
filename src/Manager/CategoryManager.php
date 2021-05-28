<?php

declare(strict_types=1);

namespace App\Manager;

require_once __DIR__.'/../../vendor/autoload.php';

use App\Exception\ResourceNotFoundException;
use App\Traits\DbInstanceTrait;
use PDO;
use Symfony\Component\Console\Exception\NamespaceNotFoundException;

class CategoryManager
{
    use DbInstanceTrait;

    public function getAllCategories(): array
    {
        $req = $this->dbInstance->prepare('SELECT * FROM category');
        $req->execute();
        return $req->fetchAll(PDO::FETCH_OBJ);
    }

    public function getCategoryById(int $id)
    {
        $req = $this->dbInstance->prepare('SELECT * FROM category WHERE id=:id');
        $req->bindValue(':id', $id);
        $req->execute();
        return $req->fetch(PDO::FETCH_ASSOC);
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
    public function deleteCategory(int $id): void
    {
        $category = $this->getCategoryById($id);
        if ($category) {
            $req = $this->dbInstance->prepare('DELETE FROM category WHERE id=:id');
            $req->bindValue(':id', $id);
            $req->execute();
        } else {
            throw new ResourceNotFoundException();
        }
    }

    /**
     * @throws ResourceNotFoundException
     */
    public function updateCategory(int $id, string $name): void
    {
        if ($category = $this->getCategoryById($id)) {
            $req = $this->dbInstance->prepare('UPDATE category SET name=:name WHERE id=:id');
            $req->bindValue(':name', $name);
            $req->bindValue(':id', $id);
            $req->execute();
        } else {
            throw new ResourceNotFoundException();
        }
    }
}
