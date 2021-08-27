<?php

declare(strict_types=1);

namespace App\HTTP;

class Request
{
    /** @var string  */
    public string $method;

    /** @var string */
    public string $uri;

    /** @var array  */
    public array $request;

    /** @var array  */
    public array $query;

    /** @var array|null  */
    public ?array $cookies;

    /** @var array|null  */
    public ?array $session;

    /** @var array|null */
    public ?array $requirements;

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
        $server = $_SERVER;
        $post = $_POST;
        $get = $_GET;
        $cookie = $_COOKIE;
        $session = $_SESSION;
        return new self(
            $server['REQUEST_METHOD'],
            strtok($server['REQUEST_URI'], '?'),
            $post,
            $get,
            $cookie,
            $session,
            []
        );
    }

    /**
     * Adds requirements to the request
     * @param $requirement
     */
    public function addRequirements($requirement): void
    {
        $this->requirements[] = $requirement;
    }
}
