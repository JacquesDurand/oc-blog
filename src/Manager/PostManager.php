<?php

declare(strict_types=1);

namespace App\Manager;

require_once __DIR__.'/../../vendor/autoload.php';

use App\Traits\DbInstanceTrait;
use PDO;

class PostManager
{
    use DbInstanceTrait;

    public function getAllPosts(): array
    {
        $req = $this->dbInstance->prepare('SELECT * FROM post');
        $req->execute();
        return $req->fetchAll(PDO::FETCH_OBJ);
    }
}
