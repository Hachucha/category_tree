<?php

namespace CategoryTree {
    use \PDO;
    function Get($pdo, $id = null, $level = null)
    {

        if($id == '')
        $id = null;

        if($level == '')
        $level = null;

        $query = "SELECT id, name, parent_id, description FROM categories";

        if ($id !== null) {
            $query .= " WHERE id=:id";
        } else {
            $query .= " WHERE parent_id IS NULL";
        }

        $stmt = $pdo->prepare($query);

        if ($id !== null) {
            $stmt->bindParam(':id', $id);
        }

        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        function getChildNodes($pdo, $parentId, $level)
        {
            $query = "SELECT id, name, parent_id, description FROM categories WHERE parent_id=:parentId";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':parentId', $parentId);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($result as &$row) {
                if ($level === null || $level > 0) {
                    $row['child'] = getChildNodes($pdo, $row['id'], $level - 1);
                }
                if (!empty($row['child'])) {
                    $row['isParent'] = true;
                } else {
                    $row['isParent'] = false;
                }
            }
            return $result;
        }

        foreach ($result as &$row) {
            if ($level === null || $level > 0) {
                $row['child'] = getChildNodes($pdo, $row['id'], $level - 1);
            }
            if (!empty($row['child'])) {
                $row['isParent'] = true;
            } else {
                $row['isParent'] = false;
            }
        }

        return $result;
    }

    function Create($pdo, $name, $parentId, $description)
    {
        if($parentId == '')
        $parentId = null;

        if($name == '' || $name === null)
        return;

        // Подготовка SQL-запроса для вставки нового элемента в таблицу дерева
        $stmt = $pdo->prepare("INSERT INTO categories (name, parent_id, description) VALUES (:name, :parent_id, :description)");

        // Привязка параметров
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':parent_id', $parentId);
        echo var_dump($parentId);
        $stmt->bindParam(':description', $description);

        // Выполнение запроса на создание нового элемента
        $stmt->execute();

        // Возврат ID нового элемента
        return $pdo->lastInsertId();
    }

    function Update($pdo, $id, $name, $parentId, $description)
    {
        if($id == '')
        return;

        if($parentId == '')
        $parentId = null;

        if($name == '' || $name === null)
        return;

        // Подготовка SQL-запроса для обновления элемента дерева по ID
        $stmt = $pdo->prepare("UPDATE categories SET name=:name, parent_id=:parent_id, description=:description WHERE id=:id");

        // Привязка параметров
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':parent_id', $parentId);
        $stmt->bindParam(':description', $description);

        // Выполнение запроса на обновление элемента
        $stmt->execute();
    }

    function Delete($pdo, $id)
    {
        if($id == '')
        return;

        // Получение дочерних элементов для данного элемента
        $stmt = $pdo->prepare("SELECT id FROM categories WHERE parent_id=:id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $children = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Рекурсивное удаление дочерних элементов
        foreach ($children as $child) {
            Delete($pdo, $child['id']);
        }

        // Подготовка SQL-запроса для удаления элемента дерева по ID
        $stmt = $pdo->prepare("DELETE FROM categories WHERE id=:id");

        // Привязка параметра
        $stmt->bindParam(':id', $id);

        // Выполнение запроса на удаление элемента
        $stmt->execute();
    }
}
