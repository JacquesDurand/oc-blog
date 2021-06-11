<?php

declare(strict_types=1);

namespace App\Routing;

use App\Authentication\Role;

class Route
{
    /** @var array */
    private array $path;

    /** @var string */
    private string $controller;

    /** @var string */
    private string $action;

    /** @var array */
    private array $methods;

    /** @var array */
    private array $requirements = [];

    /** @var int  */
    private int $authLevel = Role::ROLE_ADMIN;

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

    /**
     * @return int
     */
    public function getAuthLevel(): int
    {
        return $this->authLevel;
    }

    /**
     * @param int $authLevel
     */
    public function setAuthLevel(int $authLevel): void
    {
        $this->authLevel = $authLevel;
    }
}
