<?php

include 'autoload.php';

$env = new Environment();
if ($env->isCliMode()) {
    (new GeneratorScript())->run();
} else {
    $env->redirect('/');
}
