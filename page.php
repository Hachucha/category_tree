<?php
include_once 'category_tree.php';
include_once 'db.php';

// Проверяем метод HTTP-запроса
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  
  // Обработка запроса на создание нового элемента
  $name = $_POST['name'] ?? '';
  $description = $_POST['description'] ?? '';
  $parent_id = $_POST['parent_id'] ?? '';
  if (!empty($name)) {
    echo \CategoryTree\Create(dbConn(), $name, $parent_id, $description);
  }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['_action']) && $_GET['_action']=="edit") {
  // Обработка запроса на обновление существующего элемента
  $id = $_GET['id'] ?? '';
  $name = $_GET['name'] ?? '';
  $description = $_GET['description'] ?? '';
  $parent_id = $_GET['parent_id'] ?? '';
  if (!empty($id) && !empty($name)) {
    \CategoryTree\Update(dbConn(), $id, $name, $parent_id, $description);
  }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['_action']) && $_GET['_action']=="delete") {
  // Обработка запроса на удаление элемента
  $id = $_GET['id'] ?? '';
  if (!empty($id)) {
      \CategoryTree\Delete(dbConn(), $id);
  }
} 

$data = \CategoryTree\Get (dbConn(), null, null);

function printCategory($category)
{
    echo '
    <li>
      <div el_id="'.$category['id'].'" el_name="'.$category['name'].'" el_description="'.$category['description'].'" class="d-flex align-items-center p-2 ps-0">';

    if ($category['isParent'] == true) {
        echo '<div class="toggle pre-item me-2">-</div>';
    } else {
        echo '<div class="pre-item me-2"></div>';
    }

    echo '<div class="me-2 fs-5">' . $category['name'] . '</div>
        <button class="edit-ctg btn btn-secondary me-2 btn-sm ">Ред.</button>
        <button class="remove-ctg btn btn-secondary me-2 btn-sm ">Уд.</button>
        <button class="add-ctg btn btn-secondary me-2 btn-sm ">Доб.</button>
      </div>';

    if ($category["isParent"] == true) {
        echo '<ul>';
        foreach ($category["child"] as $ch) {
            printCategory($ch);
        }
        echo '</ul>';
    }

    echo '</li>';
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Редактирование каталога</title>
  <link rel="stylesheet" href="./libs/bootstrap.min.css">
</head>

<body>
<?php
echo '<ul id="tree" class="user-select-none p-2 m-2">
        <button class="add-ctg btn btn-secondary me-2 btn-sm">Доб.</button>';

foreach ($data as $category) {
    printCategory($category);
}
echo '</ul>';
?>

<script>
  // Get all toggle buttons
  var toggles = document.querySelectorAll(".toggle");

  // Add event listeners to each toggle button
  toggles.forEach(function (toggle) {
    toggle.addEventListener("click", function () {
      // Toggle the class 'collapsed' on the parent li element
      this.parentElement.parentElement.classList.toggle("collapsed");

      // Toggle the text of the toggle button
      if (this.innerHTML === "+") {
        this.innerHTML = "-";
      } else {
        this.innerHTML = "+";
      }
    });
  });
</script>

<script>
function showModal(html) {
  const modal = document.createElement("div");
  modal.innerHTML = html;
  // const okButton = modal.querySelector("#mod-ok");
  const cancelButton = modal.querySelector("#mod-cancel");
  const closeButton = modal.querySelector("#mod-close");
  const removeModal = () => {
    cancelButton.removeEventListener("click", onCancelClick);
    closeButton.removeEventListener("click", onCancelClick);
    modal.remove();
  };
  const onCancelClick = () => {
    removeModal();
  };
  cancelButton.addEventListener("click", onCancelClick);
  closeButton.addEventListener("click", onCancelClick);
  document.body.appendChild(modal);
}

// Add event listeners to each add-category button
var addButons = document.querySelectorAll(".add-ctg");
addButons.forEach(function (addButon) {
  addButon.addEventListener("click", function () {
    let el_id = this.parentElement.getAttribute("el_id");
    if(el_id === null)
      el_id = "";
    let el_name = this.parentElement.getAttribute("el_name");
    if(el_name === null)
      el_name = "";
    let el_description = this.parentElement.getAttribute("el_description");
    if(el_description === null)
      el_description = "";

    showModal(`
  <form method="POST" action="" class="modal" tabindex="-1" style="display: block">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Добавить категорию</h5>
          <button id="mod-close" type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="parent_id" value="`+el_id+`">
          <input name="name" class="form-control" placeholder="Название категории" type="text" id="name" value="`+el_name+`">
          
          <input name="description" class="form-control mt-2" placeholder="Описание" type="text" id="description" value="`+el_description+`">
        </div>
        <div class="modal-footer">
          <button type="button" id="mod-cancel" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
          <button type="submit" id="mod-ok" class="btn btn-primary">Ок</button>
        </div>
      </div>
    </div>
  </form>
  `)
  });
});

// Add event listeners to each edit-category button
var editButons = document.querySelectorAll(".edit-ctg");
editButons.forEach(function (editButon) {
  editButon.addEventListener("click", function () {
    let el_id = this.parentElement.getAttribute("el_id");
    if(el_id === null)
      el_id = "";
    let el_name = this.parentElement.getAttribute("el_name");
    if(el_name === null)
      el_name = "";
    let el_description = this.parentElement.getAttribute("el_description");
    if(el_description === null)
      el_description = "";
    showModal(`
  <form method="GET" action="" class="modal" tabindex="-1" style="display: block">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Редактировать категорию</h5>
          <button id="mod-close" type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id" value="`+el_id+`">
          <input name="name" class="form-control" placeholder="Название категории" type="text" id="name" value="`+el_name+`">
          <input type="hidden" name="_action" value="edit">
          <input name="description" class="form-control mt-2" placeholder="Описание" type="text" id="description" value="`+el_description+`">
        </div>
        <div class="modal-footer">
          <button type="button" id="mod-cancel" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
          <button type="submit" id="mod-ok" class="btn btn-primary">Ок</button>
        </div>
      </div>
    </div>
  </form>
  `)
  });
});

// Add event listeners to each remove-category button
var removeButons = document.querySelectorAll(".remove-ctg");
removeButons.forEach(function (removeButon) {
  removeButon.addEventListener("click", function () {
    let el_id = this.parentElement.getAttribute("el_id");
    if(el_id === null)
      el_id = "";
    let el_name = this.parentElement.getAttribute("el_name");
    if(el_name === null)
      el_name = "";
    showModal(`
  <form method="GET" action="" class="modal" tabindex="-1" style="display: block">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Удаление</h5>
          <button id="mod-close" type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
        </div>
          <span class="m-4 fs-5">Удалить категорию \"`+el_name+`\" ?</span>
          <input type="hidden" name="id" value="`+el_id+`">
          <input type="hidden" name="_action" value="delete">
        <div class="modal-footer">
          <button type="button" id="mod-cancel" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
          <button type="submit" id="mod-ok" class="btn btn-primary">Ок</button>
        </div>
      </div>
    </div>
  </form>
  `)
  });
});

</script>

<style>

    body{
      background-color: whitesmoke;
    }

    #tree{
      background-color: white;
      border-radius: 5px;
      display: inline-block;
    }

    /* Hide all sub-levels by default */

    #tree ul {
      display: none;
    }

    #tree li {
      list-style-type: none;
    }

    /* Show sub-levels when parent is not collapsed */
    #tree li:not(.collapsed)>ul {
      display: block;
    }

    /* Show toggle button for non-leaf nodes */
    #tree li>ul {
      margin-left: 1em;
    }

    #tree li:not(:last-child)>.toggle {
      display: inline-block;
      width: 1em;
      text-align: center;
    }

    .pre-item {
      width: 26px;
      line-height: 22px;
      text-align: center;
      height: 26px;

      border-radius: 3px;
    }

    .toggle:hover {
      background-color: #cfcfcf;
      cursor: pointer;
    }

    /* .crud-button{
      border-radius: 10px;
      background-color: #cfcfcf;
      cursor: pointer;
      border: none;
    } */
</style>
<script src="./libs/bootstrap.min.js"></script>

</body>

</html>