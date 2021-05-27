<?php

use App\HTTP\Request;
use App\Manager\CategoryManager;
use App\Model\Category;
use App\Routing\Router;

require_once __DIR__.'/../vendor/autoload.php';
//require __DIR__.'/../src/templates/Admin/Category/show.html.twig';

$value = "Toto";

//$managerTest = new CategoryManager();
//$managerTest->createCategory('category4');
//$categories = $managerTest->getAllCategories();
//foreach ($categories as $category) {
//    $categoryModel = new Category();
//    $categoryModel->setId($category->id);
//    $categoryModel->setName($category->name);
//}
$request = Request::createFromGlobals();
$router = new Router();
$router->handleRequest($request);

//$loader = new \Twig\Loader\FilesystemLoader(__DIR__.'/../src/templates');
//$twig = new \Twig\Environment($loader, ['debug' => true]);
//
////$twig->load('Admin/Category/show.html.twig');
//$twig->render('Admin/Category/show.html.twig', ['categories' => 'toto'] );
