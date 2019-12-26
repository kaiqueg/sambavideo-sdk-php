<?php

include "config.php";

function show(\Sambavideo\API\Entities\Category $category) {
    echo "<p>
        <b>ID</b>: {$category->getId()}
        <br/><b>Name</b>: {$category->getName()}
        <br/><b>Parent Id</b>: {$category->getParentId()}
        <br/><b>Media Count</b>: {$category->getMediaCount()}";
    $children = $category->getChildren();
    if(empty($children)) {
        echo "<br/><b>Children</b>: empty";
    } else {
        echo "<br/><details style='margin-top: -15px;'>
            <summary><b>Children</b>:</summary>
            <ul>";
        foreach ($category->getChildren() as $child) {
            echo "<li>";
            show($child);
            echo "</li>";
        }
        echo "</ul></details>";
    }
    echo "</p>";
}

$categoryId = 505; // got on category-create.php
$postFields = [
    "pid" => $projectId
];
$category = new \Sambavideo\API\Entities\Category();

echo "<h1>FETCH</h1>";
$category->fetch($categoryId, $postFields);
show($category);

echo "<h1>SAVE</h1>";
$category->setName("Teste Alteração");
$category->save($postFields);
show($category);