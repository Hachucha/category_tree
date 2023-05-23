<?php
include_once '../category_tree.php';
include_once '../db.php';

// Обработка запроса на удаление элемента
$id = $_POST['id'] ?? '';
if (!empty($id)) {
    \CategoryTree\Delete(dbConn(), $id);
}

header("Location: ../index.php");
die();