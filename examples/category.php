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

$list = [];
// SEARCH
echo "<h1>SEARCH</h1>";
$list = (new \Sambavideo\API\Entities\Category())->search([
    "pid" => $projectId,
    "parent" => 0,
]);

foreach($list as $category) {
    show($category);
}
// FETCH
echo "<hr/><h1>FETCH</h1>";
if(empty($list)) {
    echo "<p>Can't fetch without an id</p>";
} else {
    $category = new \Sambavideo\API\Entities\Category();
    $category->fetch(
        $list[0]->getId(),
        [
            "pid" => $projectId,
        ]
    );
    show($category);
}