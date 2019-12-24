<?php

include "config.php";

$API = new \Sambavideo\API\Entities\Project();

$list = [];
try {
    $list = $API->search();
} catch(Exception $e) {
    exit($e->getMessage());
}

foreach($list as $project) {
    /** @var \Sambavideo\API\Entities\Project $project */
    $desc = $project->getDescription();
    if($desc) {
        $desc = ": $desc";
    }
    echo "<p><b>#{$project->getId()} {$project->getName()}</b>$desc</p>";
}