<?php

namespace App;

require_once __DIR__.'/../DependencyInjection/DbSingleton.php';


use App\DependencyInjection\DbSingleton;
use PDO;

trait DbInstanceTrait
{
    /** @var PDO  */
    protected $dbInstance;

    public function __construct()
    {
        $this->dbInstance = DbSingleton::getInstance()->getConnection();
    }
}
