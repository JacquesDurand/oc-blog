<?php

namespace App\Traits;

require_once __DIR__.'/../../vendor/autoload.php';


use App\DependencyInjection\DbSingleton;
use PDO;

trait DbInstanceTrait
{
    /** @var PDO  */
    protected PDO $dbInstance;

    /**
     * Connects to the Db instance and sets attributes
     */
    public function connect()
    {
        $this->dbInstance = DbSingleton::getInstance()->getConnection();
        $this->dbInstance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
}
