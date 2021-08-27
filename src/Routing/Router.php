<?php

declare(strict_types=1);

namespace App\Routing;

use App\Authentication\Role;
use App\HTTP\Request;

require_once __DIR__.'/../../vendor/autoload.php';


class Router
{
    /** @var Route[] */
    private array $routes;

    public function __construct()
    {
        $this->loadRoutes();
    }

    /**
     * Sends to the correct Controller and Action depending on the Request
     * @param Request $request
     * @return mixed
     */
    public function handleRequest(Request $request)
    {
        if (null === $route = $this->getRoute($request)) {
            header('Location: http://localhost/');
        } else {
            if (!\in_array($request->method, $route->getMethods())) {
                header('Location: http://localhost/');
            }
            $this->checkAuth($route);
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
     * Returns the correct Route if it exists
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
                            continue 2;
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

    /**
     * Loads the different routes from the Yaml config files
     */
    private function loadRoutes(): void
    {
        $apiRoutes = \yaml_parse_file(__DIR__ . '/../../config/api_routes.yaml');
        $adminRoutes = \yaml_parse_file(__DIR__ . '/../../config/admin_routes.yaml');
        $this->createRoutes($adminRoutes, true);
        $this->createRoutes($apiRoutes);
    }

    /**
     * Creates Routes from the Yaml config files
     * @param array $routes
     * @param bool $isAdmin
     */
    private function createRoutes(array $routes, bool $isAdmin = false): void
    {
        foreach ($routes as $routeToLoad) {
            $route = new Route();
            if ($isAdmin) {
                $route->setAdminPath($routeToLoad['path']);
                $route->setAuthLevel(Role::ROLE_ADMIN);
            } else {
                $route->setApiPath($routeToLoad['path']);
                if (isset($routeToLoad['auth']) && !empty($routeToLoad['auth'])) {
                    switch ($routeToLoad['auth']) {
                        case 'verified':
                            $route->setAuthLevel(Role::ROLE_USER_VERIFIED);
                            break;
                        case 'admin':
                            $route->setAuthLevel(Role::ROLE_ADMIN);
                            break;
                    }
                } else {
                    $route->setAuthLevel(Role::ROLE_REMOVED);
                }
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

    /**
     * Checks if the User has enough credentials for the requested Route
     * @param Route $route
     */
    private function checkAuth(Route $route)
    {
        if (Role::ROLE_REMOVED === $route->getAuthLevel()) {
            return;
        }
        if (!isset($_SESSION['userId']) || empty($_SESSION['userId'])) {
            header('Location: http://localhost/login');
        } else {
            switch ($route->getAuthLevel()) {
                case Role::ROLE_USER_VERIFIED:
                    if (!isset($_SESSION['role']) || empty($_SESSION['role']) || Role::ROLE_USER_VERIFIED > $_SESSION['role']) {
                        header('Location: http://localhost/login');
                    } else {
                        return;
                    }
                    break;
                case Role::ROLE_ADMIN:
                    if (!isset($_SESSION['role']) || empty($_SESSION['role']) || Role::ROLE_ADMIN > $_SESSION['role']) {
                        header('Location: http://localhost/login');
                    } else {
                        return;
                    }
            }
        }
    }
}
