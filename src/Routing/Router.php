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
        if (null === $route = $this->getRoute($request)) {
            echo 'Dommage';
        } else {
            if (!\in_array($request->method, $route->getMethods())) {
                echo 'Mauvaise methode';
            }
            //Ici authent middleware
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
     * @param Request $request
     * @return Route|null
     */
    public function getRoute(Request $request): ?Route
    {
        $explodedUri = explode('/', $request->uri);
        array_shift($explodedUri);

        foreach ($this->getRoutes() as $route) {
            if (count($explodedUri) !== count($route->getPath())) {
                continue;
            }
            foreach ($route->getPath() as $index => $pathPiece) {
                if ($pathPiece !== $explodedUri[$index]) {
                    if (str_starts_with($pathPiece, ':')) {
                        $requirements = $route->getRequirements()[str_replace(':', '', $pathPiece)];
                        if (!\preg_match($requirements, $explodedUri[$index])) {
                            return null;
                        } else {
                            $request->addRequirements($explodedUri[$index]);
                        }
                    } else {
                        continue 2;
                    }
                }
            }
            return $route;
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

    private function loadRoutes(): void
    {
        $apiRoutes = \yaml_parse_file(__DIR__ . '/../../config/api_routes.yaml');
        $adminRoutes = \yaml_parse_file(__DIR__ . '/../../config/admin_routes.yaml');
        $this->createRoutes($adminRoutes, true);
        $this->createRoutes($apiRoutes);
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
            if (!empty($routeToLoad['requirements'])) {
                foreach ($routeToLoad['requirements'] as $requirement) {
                    $route->addRequirement($requirement);
                }
            }

            $this->addRoute($route);
        }
    }
}
