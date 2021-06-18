<?php

const FULL_MODE_SUFFIX = 'full';
const CONTENT_MODE_SUFFIX = 'content';

const DOMAIN_PATTERN = '/patrons([.][a-z]+)?[.]space$/';
const PROJECT_DIR = '/patrons-space/';

const RESOURCES_PATH = '/resources/';
const HTML_TEMPLATES_PATH = RESOURCES_PATH . 'html/';
const STYLES_PATH = RESOURCES_PATH . 'css/';
const PHP_PATH = RESOURCES_PATH . 'php/';

$host = $_SERVER['SERVER_NAME'];
$language = rtrim(preg_replace(DOMAIN_PATTERN, '', $host), '.');

$rootPath = preg_replace('~' . PROJECT_DIR . '.*~', PROJECT_DIR, __FILE__);
$requestPath = rtrim($_SERVER['REQUEST_URI'], '/');
$lastRequestPathElement = basename($requestPath);

if ($lastRequestPathElement === 'content') {
    $suffix = CONTENT_MODE_SUFFIX;
} else {
    $suffix = FULL_MODE_SUFFIX;
}

$htmlPath = $rootPath . HTML_TEMPLATES_PATH . 'index.html';
$bodyPath = $rootPath . HTML_TEMPLATES_PATH . "body-$suffix.html";
$stylePath = STYLES_PATH . "style-$suffix.css";

$html = file_get_contents($htmlPath);
$body = file_get_contents($bodyPath);

require($rootPath . PHP_PATH . 'content.php');
$contentObj = new Content();
$title = $contentObj->getTitle();
$content = $contentObj->getContent();

$html = str_replace('#TITLE#', $title, $html);
$html = str_replace('#STYLE#', $stylePath, $html);
$html = str_replace('#BODY#', $body, $html);
$html = str_replace('#CONTENT#', $content, $html);

echo $html;
