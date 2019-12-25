<?php

include "config.php";

function show(\Sambavideo\API\Entities\Media $media, $projectId) {
    $desc = $media->getDescription();
    if($desc) {
        $desc = ": $desc";
    }
    echo "<p>
        <b>ID</b>: {$media->getId()}
        <br/><b>Title</b>: {$media->getTitle()}
        </br><b>Description</b>: {$media->getDescription()}
        <br/><b>Short Description</b>: {$media->getShortDescription()}
        <br/><b>Duration (ms)</b>: {$media->getDurationMilliseconds()}
        <br/><b>Duration (time)</b>: {$media->getDurationTime()}
        <br/><b>-Files</b>:<ul>";
    $files = $media->getFiles();
    foreach($files as $file) {
        echo "<li><b>{$file['outputName']}</b>: {$file['url']}</li>";
    }
    echo "</ul><br/><b>Thumbs</b>:<ul>";
    $thumbs = $media->getThumbs();
    foreach($thumbs as $thumb) {
        echo "<li><b>{$thumb['width']}x{$thumb['height']}</b>: {$thumb['url']}</li>";
    }
    echo "</ul>
        <br/><b>URL</b>: {$media->getEmbedUrl($projectId)}
        <br/><b>Iframe</b>: {$media->getIframe($projectId)}
    </p>";
}

$list = [];
// SEARCH
echo "<h1>SEARCH</h1>";
$list = (new \Sambavideo\API\Entities\Media())->search([
    "pid" => $projectId,
]);
foreach($list as $media) {
    show($media, $projectId);
}
// FETCH
echo "<hr/><h1>FETCH</h1>";
if(empty($list)) {
    echo "<p>Can't fetch without an id</p>";
} else {
    $media = new \Sambavideo\API\Entities\Media();
    $media->fetch(
        $list[0]->getId(),
        [
            "pid" => $projectId,
        ]
    );
    show($media, $projectId);
}