<?php
session_start();
use App\HTTP\Request;
use App\Manager\CategoryManager;
use App\Model\Category;
use App\Routing\Router;

require_once __DIR__.'/../vendor/autoload.php';

$request = Request::createFromGlobals();
$router = new Router();
$router->handleRequest($request);

