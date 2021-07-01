<?php

spl_autoload_register(function ($class) {
    if (substr($class, -9) === 'Interface') {
        include dirname(__FILE__) . '/interfaces/' . $class . '.php';
    } else {
        include dirname(__FILE__) . '/classes/' . $class . '.php';
    }
});
