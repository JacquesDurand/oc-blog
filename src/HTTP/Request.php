<?php

declare(strict_types=1);

namespace App\HTTP;

class Request
{
    /** @var string  */
    public $method;

    /** @var string */
    public $uri;

    /** @var array  */
    public $request;

    /** @var array  */
    public $query;

    /** @var array|null  */
    public $cookies;

    /** @var array|null  */
    public $session;

    /** @var array|null */
    public $requirements;

    public function __construct(
        string $method,
        string $uri,
        array $request,
        array $query,
        ?array $cookies,
        ?array $session,
        ?array $requirements
    ) {
        $this->method = $method;
        $this->uri = $uri;
        $this->request = $request;
        $this->query = $query;
        $this->cookies = $cookies;
        $this->session = $session;
        $this->requirements = $requirements;
    }

    /**
     * Create a request object from superGlobals
     * @return Request
     */
    public static function createFromGlobals(): Request
    {
        return new self(
            $_SERVER['REQUEST_METHOD'],
            strtok($_SERVER['REQUEST_URI'], '?'),
            $_POST,
            $_GET,
            $_COOKIE,
            $_SESSION,
            []
        );
    }

    public function addRequirements($requirement): void
    {
        $this->requirements[] = $requirement;
    }
}
