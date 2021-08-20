<?php

include 'autoload.php';

$env = new Environment();
if ($env->isCliMode()) {
    (new GeneratorProcedure())->run();
} else {
    $env->redirect('/');
}
