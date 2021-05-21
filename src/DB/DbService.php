<?php

declare(strict_types=1);

namespace App\DB;

use Dotenv\Dotenv;

require_once(__DIR__ .'/../../vendor/autoload.php');

$dotenv = Dotenv::createImmutable(__DIR__.'/../..');
$dotenv->load();

class DbService
{
    /** @var string */
    private $host;

    /** @var int */
    private $port;

    /** @var string */
    private $dbName;

    /** @var string */
    private $userName;

    /** @var string */
    private $password;

    public function __construct()
    {
        $this->setHost($_ENV['DB_HOST']);
        $this->setPort((int) $_ENV['DB_PORT']);
        $this->setDbName($_ENV['DB_NAME']);
        $this->setUserName($_ENV['DB_USERNAME']);
        $this->setPassword($_ENV['DB_PASSWORD']);
    }

    public function connect(): \PDO
    {
        return new \PDO(
            \sprintf('pgsql:host=%s;port=%d;dbname=%s', $this->getHost(),$this->getPort(),$this->getDbName()),
            $this->getUserName(),
            $this->getPassword()
        );
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @param string $host
     */
    public function setHost(string $host)
    {
        $this->host = $host;
    }

    /**
     * @return int
     */
    public function getPort(): int
    {
        return $this->port;
    }

    /**
     * @param int $port
     */
    public function setPort(int $port)
    {
        $this->port = $port;
    }

    /**
     * @return string
     */
    public function getDbName(): string
    {
        return $this->dbName;
    }

    /**
     * @param string $dbName
     */
    public function setDbName(string $dbName)
    {
        $this->dbName = $dbName;
    }

    /**
     * @return string
     */
    public function getUserName(): string
    {
        return $this->userName;
    }

    /**
     * @param string $userName
     */
    public function setUserName(string $userName)
    {
        $this->userName = $userName;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password)
    {
        $this->password = $password;
    }

}