<?php

spl_autoload_register(function ($class) {
    include dirname(__FILE__) . '/classes/' . $class . '.class.php';
});
