<?php

require_once realpath(implode(DIRECTORY_SEPARATOR, [__DIR__, "..", "src", "spl_autoload.php"]));

\Sambavideo\API\Settings::setToken("YOUR-TOKEN");

// put project's data from the output of project.php
$projectId = 0;