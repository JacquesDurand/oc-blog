<?php

declare(strict_types=1);

namespace App\DependencyInjection;

require_once __DIR__.'/../../vendor/autoload.php';

use App\DB\DbService;
use PDO;

class DbSingleton
{
    /** @var DbSingleton */
    private static DbSingleton $instance;

    /** @var DbService */
    private DbService $dbService;

    protected function __construct()
    {
        $this->dbService = new DbService();
    }

    /**
     * Creates a new Db connection if not existing
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
