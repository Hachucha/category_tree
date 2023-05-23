<?php
include_once '../category_tree.php';
include_once '../db.php';

// Обработка запроса на создание нового элемента
$name = $_POST['name'] ?? '';
$description = $_POST['description'] ?? '';
$parent_id = $_POST['parent_id'] ?? '';
if (!empty($name)) {
  \CategoryTree\Create(dbConn(), $name, $parent_id, $description);
}



header("Location: ../index.php");
die();