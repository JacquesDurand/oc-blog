<?php

use App\HTTP\Request;
use App\Manager\CategoryManager;
use App\Model\Category;
use App\Routing\Router;

require_once __DIR__.'/../vendor/autoload.php';

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
$categories = $router->handleRequest($request);

?>

<html>
<body>
<h1>Hello, <?= $value ?>!</h1>

<?php foreach($categories as $row): ?>
    <p>Hello, <?= $row->name ?></p>
<?php endforeach; ?>
</body>
</html>