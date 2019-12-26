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

$category = new \Sambavideo\API\Entities\Category();
$category->setName("Teste");
$category->setParentId(1);
$category->save([
    "pid" => $projectId,
]);
show($category);

// on my example, generated a category with id = 505