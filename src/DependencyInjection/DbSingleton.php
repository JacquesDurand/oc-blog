<?php

declare(strict_types=1);

namespace App\DependencyInjection;

require_once __DIR__.'/../DB/DbService.php';

use App\DB\DbService;
use PDO;

class DbSingleton
{
    /** @var DbSingleton */
    private static $instance;

    /** @var DbService */
    private $dbService;

    protected function __construct()
    {
        $this->dbService = new DbService();
    }

    /**
     * @return static
     */
    public static function getInstance(): DbSingleton
    {
        if (!self::$instance) {
            self::$instance = new DbSingleton();
        }

        return self::$instance;
    }

    /**
     *@return PDO
     */
    public function getConnection(): PDO
    {
        return $this->dbService->connect();
    }
}
