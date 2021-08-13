<?php

declare(strict_types=1);

namespace App\DB;

require_once(__DIR__ .'/../../vendor/autoload.php');

use Dotenv\Dotenv;
use PDO;

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
        $host = $_ENV['DB_HOST'];
        $port = (int) $_ENV['DB_PORT'];
        $name = $_ENV['DB_NAME'];
        $username = $_ENV['DB_USERNAME'];
        $password = $_ENV['DB_PASSWORD'];

        $this->setHost($host);
        $this->setPort($port);
        $this->setDbName($name);
        $this->setUserName($username);
        $this->setPassword($password);
    }

    public function connect(): PDO
    {
        try {
            return new PDO(
                \sprintf('pgsql:host=%s;port=%d;dbname=%s', $this->getHost(), $this->getPort(), $this->getDbName()),
                $this->getUserName(),
                $this->getPassword()
            );
        } catch (\PDOException $exception) {
            ##TODO implement better catch
            var_dump($exception->getMessage());
        }
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
