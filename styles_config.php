<?php
header("Content-Type: text/css; charset: UTF-8");

include_once 'config.php';

echo ':root {' . PHP_EOL;

// If else case list
echo '--theme-bg-color: '. (defined('THEMEBGCOLOR') ? THEMEBGCOLOR : '#f7f7ff'). ';' . PHP_EOL;
echo '--theme-bg-loader: ' . (defined('TAOH_LOADER_GIF') ? "url('" . TAOH_LOADER_GIF . "')" : "url('" . TAOH_SITE_URL_ROOT . "/assets/images/ripples.svg')") . ';' . PHP_EOL;

// Only If case list
if(defined('THEMEFONTFAMILY')) echo  '--theme-font-family: ' . THEMEFONTFAMILY . ';' . PHP_EOL;
if(defined('THEMEFONTSIZE')) echo  '--theme-font-size: ' . THEMEFONTSIZE . ';' . PHP_EOL;
if(defined('THEMEFONTWEIGHT')) echo  '--theme-font-weight: ' . THEMEFONTWEIGHT . ';' . PHP_EOL;

echo '}' . PHP_EOL;
?>