<?php

declare(strict_types=1);


use App\DB\DbService;

class CategoryManager
{
    /** @var DbService */
    private $dbService;

    public function __construct(DbService $dbService)
    {
        $this->dbService = $dbService;
    }

    public function getAllCategories(): array
    {
        $db = $this->dbService->connect();
        $req = $db->prepare('SELECT * FROM category');
        $req->execute();
        return $req->fetchAll(PDO::FETCH_OBJ);
    }

}