<?php
$id = $news->clearInt($_GET["item_id"]);


if (!$news->deleteNews($id)) {
    $errMsg = "Произошла ошибка при удалении";
} else {
    header("Location: index.php");
    exit;
}
?>