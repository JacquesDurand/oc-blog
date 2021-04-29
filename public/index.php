<?php

$value = "World";

$db = new PDO('pgsql:host=db;port=5432;dbname=mydb', 'legging', 'root');

$databaseTest = ($db->query('SELECT * FROM dockerSample'))->fetchAll(PDO::FETCH_OBJ);

?>

<html>
<body>
<h1>Hello, <?= $value ?>!</h1>

<?php foreach($databaseTest as $row): ?>
    <p>Hello, <?= $row->name ?></p>
<?php endforeach; ?>
</body>
</html>