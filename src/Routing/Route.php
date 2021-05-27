<?php

declare(strict_types=1);

namespace App\Routing;

class Route
{
    /** @var array */
    private $path;

    /** @var string */
    private $controller;

    /** @var string */
    private $action;

    /** @var array */
    private $methods;

    /** @var array */
    private $requirements = [];

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
     * @return array
     */
    public function getPath(): array
    {
        return $this->path;
    }

    /**
     * @param string $path
     */
    public function setAdminPath(string $path): void
    {
        $this->path = explode('/', self::ADMIN_PREFIX.$path);
        array_shift($this->path);
    }

    /**
     * @param string $path
     */
    public function setApiPath(string $path): void
    {
        $this->path = explode('/', $path);
        array_shift($this->path);
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

    /**
     * @return array
     */
    public function getRequirements(): array
    {
        return $this->requirements;
    }

    public function hasRequirements(): bool
    {
        return (!empty($this->requirements));
    }

    /**
     * @param array $requirement
     */
    public function addRequirement(array $requirement): void
    {
        if ($requirement) {
            $this->requirements = array_merge($this->requirements, $requirement);
        }
    }
}
