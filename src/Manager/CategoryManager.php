<?php

declare(strict_types=1);

namespace App\Manager;

require_once __DIR__.'/../Trait/DbInstanceTrait.php';

use App\DbInstanceTrait;
use PDO;

class CategoryManager
{
    use DbInstanceTrait;

    public function getAllCategories(): array
    {
        $req = $this->dbInstance->prepare('SELECT * FROM category');
        $req->execute();
        return $req->fetchAll(PDO::FETCH_OBJ);
    }

    public function createCategory(string $name): void
    {
        $req = $this->dbInstance->prepare('INSERT INTO category(name) VALUES (:name)');
        $req->bindValue(':name', $name);
        $req->execute();
    }
}
