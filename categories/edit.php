<?php
include_once '../category_tree.php';
include_once '../db.php';

// Обработка запроса на обновление существующего элемента
$id = $_POST['id'] ?? '';
$name = $_POST['name'] ?? '';
$description = $_POST['description'] ?? '';
$parent_id = $_POST['parent_id'] ?? '';
if (!empty($id) && !empty($name)) {
  \CategoryTree\Update(dbConn(), $id, $name, $parent_id, $description);
}

header("Location: ../index.php");
die();