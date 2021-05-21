<?php

declare(strict_types=1);

namespace App\Manager;

require_once __DIR__.'/../../vendor/autoload.php';

use App\Traits\DbInstanceTrait;
use PDO;

class UserManager
{
    use DbInstanceTrait;

    public function getAllUsers(): array
    {
        $req = $this->dbInstance->prepare('SELECT * FROM users');
        $req->execute();
        return $req->fetchAll(PDO::FETCH_OBJ);
    }
}
