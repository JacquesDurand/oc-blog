<?php

declare(strict_types=1);

namespace App\Routing;

use App\HTTP\Request;

require_once __DIR__.'/../../vendor/autoload.php';


class Router
{
    /** @var Route[] */
    private $routes;

    public function __construct()
    {
        $this->loadRoutes();
    }

    ##TODO rework this function for errors
    public function handleRequest(Request $request)
    {
        if (null === $route = $this->getRoute($request->uri)) {
            echo 'Dommage';
        } else {
            if (!\in_array($request->method, $route->getMethods())) {
                echo 'Mauvaise methode';
            }
            $controller = $route->getController();
            $controller = new $controller();
            $action = $route->getAction();
            return $controller->$action($request);
        }
    }

    /**
     * @return Route[]
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }

    /**
     * @param string $uri
     * @return Route|null
     */
    public function getRoute(string $uri): ?Route
    {
        foreach ($this->routes as $route) {
            if ($uri === $route->getPath()) {
                return $route;
            }
        }
        return null;
    }

    /**
     *@param Route $route
     */
    public function addRoute(Route $route): void
    {
        $this->routes[] = $route;
    }

    /**
     * @param string $uri
     * @return bool
     */
    public function hasRoute(string $uri): bool
    {
        foreach ($this->routes as $route) {
            if ($uri === $route->getPath()) {
                return true;
            }
        }
        return false;
    }

    private function loadRoutes(): void
    {
        $apiRoutes = \yaml_parse_file(__DIR__ . '/../../config/api_routes.yaml');
        $adminRoutes = \yaml_parse_file(__DIR__ . '/../../config/admin_routes.yaml');
        $this->createRoutes($apiRoutes);
        $this->createRoutes($adminRoutes, true);
    }

    private function createRoutes(array $routes, bool $isAdmin = false): void
    {
        foreach ($routes as $routeToLoad) {
            $route = new Route();
            if ($isAdmin) {
                $route->setAdminPath($routeToLoad['path']);
            } else {
                $route->setApiPath($routeToLoad['path']);
            }
            $route->setController($routeToLoad['controller']);
            $route->setAction($routeToLoad['action']);
            foreach ($routeToLoad['methods'] as $method) {
                $route->addMethod($method);
            }

            $this->addRoute($route);
        }
    }
}
