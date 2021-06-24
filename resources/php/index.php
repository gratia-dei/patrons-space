<?php

require('classes/Paths.php');
require('classes/ContentBase.php');
require('classes/Content.php');

$pathsObj = new Paths();
$contentObj = new Content($pathsObj);
$contentBaseObj = new ContentBase($contentObj);

const FULL_MODE_SUFFIX = 'full';
const CONTENT_MODE_SUFFIX = 'content';

const DOMAIN_PATTERN = '/patrons([.][a-z]+)?[.]space$/';

$httpStatusCode = $_SERVER['REDIRECT_STATUS'];
$protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
$host = $_SERVER['SERVER_NAME'];
$language = rtrim(preg_replace(DOMAIN_PATTERN, '', $host), '.');
$domain = ltrim(mb_substr($host, mb_strlen($language)), '.');
$requestPath = rtrim($_SERVER['REQUEST_URI'], '/');

if (basename($requestPath) === CONTENT_MODE_SUFFIX) {
    $suffix = CONTENT_MODE_SUFFIX;
} else {
    $suffix = FULL_MODE_SUFFIX;
}
$htmlPath = $pathsObj->getHtmlPath(true) . 'index.html';
$bodyPath = $pathsObj->getHtmlPath(true) . "body-$suffix.html";
$selectableLanguagesListPath = $pathsObj->getHtmlPath(true) . 'selectable-languages-list-item.html';
$stylePath = $pathsObj->getStylesPath() . "style-$suffix.css";
$languageVarsPath = $pathsObj->getDataPath(true) . 'website-language-variables.json';
$languagesPath = $pathsObj->getDataPath(true) . 'languages.json';

$contentBaseObj->setLanguages($language, $languageVarsPath, $languagesPath);

$bodyVars = [
    'content' => $contentBaseObj->getContent($requestPath, $httpStatusCode),
    'selected-language' => $contentBaseObj->getSelectedLanguage(),
    'selectable-languages-list' => $contentBaseObj->getSelectableLanguagesList(
        $protocol,
        $domain,
        $requestPath,
        $selectableLanguagesListPath
    ),
];
$body = $contentBaseObj->getFileContent($bodyPath, $bodyVars);

$htmlVars = [
    'title' => $contentBaseObj->getTitle(),
    'style' => $stylePath,
    'body' => $body,
];
$html = $contentBaseObj->getFileContent($htmlPath, $htmlVars);

$websiteTranslatedVariables = $contentBaseObj->getWebsiteTranslatedVariables();
$html = $contentBaseObj->getReplacedContent($html, $websiteTranslatedVariables, true);

echo $html;
