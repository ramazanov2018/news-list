<?php
if ($posts = $news->getNews()){
    foreach ($posts as $key=>$post) {?>
        <div class="post">
            <h2><?=$post["title"]?></span></h2>
            <p><?=$post["description"]?></p>
            <p><span>Дата добавления: <?=date('Y-m-d',$post["datetime"])?>. Категория: <?=$post["category"]?></p>
            <p><a href="<?=$post["source"]?>">Источник</a></p>
        </div>
    <?}
}else{
    $errMsg = "Произошла ошибка при выводе новостной ленты";
}
?>