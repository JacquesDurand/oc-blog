<?php

namespace App\Traits;

require_once __DIR__.'/../../vendor/autoload.php';


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
