<?php

use App\Manager\CategoryManager;
use App\Model\Category;

require_once __DIR__.'/../vendor/autoload.php';

$value = "Toto";

$managerTest = new CategoryManager();
$managerTest->createCategory('category4');
$categories = $managerTest->getAllCategories();
foreach ($categories as $category) {
    $categoryModel = new Category();
    $categoryModel->setId($category->id);
    $categoryModel->setName($category->name);
}
var_dump($categoryModel);
?>

<html>
<body>
<h1>Hello, <?= $value ?>!</h1>

<?php foreach($categories as $row): ?>
    <p>Hello, <?= $row->name ?></p>
<?php endforeach; ?>
</body>
</html>