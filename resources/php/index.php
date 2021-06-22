<?php

const FULL_MODE_SUFFIX = 'full';
const CONTENT_MODE_SUFFIX = 'content';

const DOMAIN_PATTERN = '/patrons([.][a-z]+)?[.]space$/';
const PROJECT_DIR = '/patrons-space/';

const ROOT_PATH = '/';
const RESOURCES_PATH = ROOT_PATH . 'resources/';
const DATA_PATH = ROOT_PATH . 'data/';
const HTML_TEMPLATES_PATH = RESOURCES_PATH . 'html/';
const PHP_PATH = RESOURCES_PATH . 'php/';
const STYLES_PATH = RESOURCES_PATH . 'css/';

$protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
$host = $_SERVER['SERVER_NAME'];
$language = rtrim(preg_replace(DOMAIN_PATTERN, '', $host), '.');
$domain = ltrim(mb_substr($host, mb_strlen($language)), '.');
$requestPath = rtrim($_SERVER['REQUEST_URI'], '/');
$lastRequestPathElement = basename($requestPath);

if ($lastRequestPathElement === CONTENT_MODE_SUFFIX) {
    $suffix = CONTENT_MODE_SUFFIX;
} else {
    $suffix = FULL_MODE_SUFFIX;
}
$rootPath = preg_replace('~' . PROJECT_DIR . '.*~', PROJECT_DIR, __FILE__);
$htmlPath = $rootPath . HTML_TEMPLATES_PATH . 'index.html';
$bodyPath = $rootPath . HTML_TEMPLATES_PATH . "body-$suffix.html";
$selectableLanguagesListPath = $rootPath . HTML_TEMPLATES_PATH . 'selectable-languages-list-item.html';
$stylePath = STYLES_PATH . "style-$suffix.css";
$languageVarsPath = $rootPath . DATA_PATH . 'website-language-variables.json';
$languagesPath = $rootPath . DATA_PATH . 'languages.json';

require($rootPath . PHP_PATH . '/content.php');
$contentObj = new Content($language, $languageVarsPath, $languagesPath);

$bodyVars = [
    'content' => $contentObj->getContent(),
    'selected-language' => $contentObj->getSelectedLanguage(),
    'selectable-languages-list' => $contentObj->getSelectableLanguagesList(
        $protocol,
        $domain,
        $requestPath,
        $selectableLanguagesListPath
    ),
];
$body = $contentObj->getFileContent($bodyPath, $bodyVars);

$htmlVars = [
    'title' => $contentObj->getTitle(),
    'style' => $stylePath,
    'body' => $body,
];
$html = $contentObj->getFileContent($htmlPath, $htmlVars);

$websiteTranslatedVariables = $contentObj->getWebsiteTranslatedVariables();
$html = $contentObj->getReplacedContent($html, $websiteTranslatedVariables, true);

echo $html;
