<?php

declare(strict_types=1);


use App\DB\DbService;

class DbSingleton
{
    private static $instance;

    /** @var DbService */
    private $dbService;

    protected function __construct()
    {
        $this->dbService = new DbService();
    }

    /**
     * @return mixed
     */
    public static function getInstance()
    {
        if(!self::$instance)
        {
            self::$instance = new DbSingleton();
        }

        return self::$instance;
    }

    /**
     *@return \PDO
     */
    public function getConnection(): \PDO
    {
        return $this->dbService->connect();
    }

}