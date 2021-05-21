<?php

declare(strict_types=1);

namespace App\Manager;

require_once __DIR__.'/../Trait/DbInstanceTrait.php';

use App\DbInstanceTrait;
use PDO;

class CommentManager
{
    use DbInstanceTrait;

    public function getAllComments(): array
    {
        $req = $this->dbInstance->prepare('SELECT * FROM comment');
        $req->execute();
        return $req->fetchAll(PDO::FETCH_OBJ);
    }
}
