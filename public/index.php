<?php

use App\Manager\CategoryManager;

require_once __DIR__.'/../vendor/autoload.php';

$value = "World";

$managerTest = new CategoryManager();
$managerTest->createCategory('category4');
$databaseTest = $managerTest->getAllCategories();

?>

<html>
<body>
<h1>Hello, <?= $value ?>!</h1>

<?php foreach($databaseTest as $row): ?>
    <p>Hello, <?= $row->name ?></p>
<?php endforeach; ?>
</body>
</html>