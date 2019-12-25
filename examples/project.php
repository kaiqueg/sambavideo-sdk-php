<?php

include "config.php";

function show(\Sambavideo\API\Entities\Project $project) {
    echo "<p>
        <b>ID</b>: {$project->getId()}
        <br/><b>Player Hash</b>: {$project->getPlayerHash()}
        <br/><b>Name</b>: {$project->getName()}
        <br/><b>Description</b>: {$project->getDescription()}
    </p>";
}

$list = [];
// SEARCH
echo "<h1>SEARCH</h1>";
$list = (new \Sambavideo\API\Entities\Project())->search();

foreach($list as $project) {
    show($project);
}
// FETCH
echo "<hr/><h1>FETCH</h1>";
if(empty($list)) {
    echo "<p>Can't fetch without an id</p>";
} else {
    $project = new \Sambavideo\API\Entities\Project();
    $project->fetch($list[0]->getId());
    show($project);
}