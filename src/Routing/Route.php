<?php

declare(strict_types=1);

namespace App\Routing;

class Route
{
    /** @var string */
    private $path;

    /** @var string */
    private $controller;

    /** @var string */
    private $action;

    /** @var array */
    private $methods;

    private const API_PREFIX = '/api';
    private const ADMIN_PREFIX = '/admin';


    /**
     * @return string
     */
    public function getController(): string
    {
        return $this->controller;
    }

    /**
     * @param string $controller
     */
    public function setController(string $controller): void
    {
        $this->controller = $controller;
    }

    /**
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * @param string $action
     */
    public function setAction(string $action): void
    {
        $this->action = $action;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param string $path
     */
    public function setAdminPath(string $path): void
    {
        $this->path = self::ADMIN_PREFIX.$path;
    }

    /**
     * @param string $path
     */
    public function setApiPath(string $path): void
    {
        $this->path = self::API_PREFIX.$path;
    }

    /**
     * @return array
     */
    public function getMethods(): array
    {
        return $this->methods;
    }

    /**
     * @param string $method
     *
     */
    public function addMethod(string $method): void
    {
        $this->methods[] = $method;
    }
}
