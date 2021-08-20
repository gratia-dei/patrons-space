<?php

spl_autoload_register(function ($class) {
    if (substr($class, -9) === 'Interface') {
        include dirname(__FILE__) . '/interfaces/' . $class . '.php';
    } else if (substr($class, -12) === 'ContentBlock') {
        include dirname(__FILE__) . '/classes/ContentBlocks/' . $class . '.php';
    } else if (substr($class, -11) === 'MainContent') {
        include dirname(__FILE__) . '/classes/MainContents/' . $class . '.php';
    } else if (substr($class, -9) === 'Procedure') {
        include dirname(__FILE__) . '/classes/Procedures/' . $class . '.php';
    } else if (substr($class, -9) === 'Exception') {
        include dirname(__FILE__) . '/classes/Exceptions/' . $class . '.php';
    } else {
        include dirname(__FILE__) . '/classes/' . $class . '.php';
    }
});
