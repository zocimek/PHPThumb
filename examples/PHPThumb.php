<?php
require_once '../vendor/autoload.php';
// SETTINGS
//
// To squeak a few more KB of memory performance out of this script replace the dynamic settings
// here with static paths.

//
// The path to PHPThumb
use PhpThumb\PhpThumbFactory;

//
// The path to your web root
$document_path = __DIR__;
//
// The path to your cache folder
$cache_path = $document_path . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR;

// The URI to your cache folder
$cache_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/' .str_replace('PHPThumb.php', '', $_SERVER['PHP_SELF']).'cache/';

//
// How long caches should live. Remember, hard refreshes will also clear out your cache so you'll be
// safe setting this pretty high
$cache_life = '-1 month';

// End configurable settings

$params = array('src'=>false, 'w'=>false, 'h'=>false);
$options = array('resizeUp'=>true,'jpegQuality'=>100,'cache_life'=>$cache_life);
extract(array_merge($params, $options, $_GET));

$cache = md5($src.$w.$h);
$cache_path.= $cache;

if (
    file_exists($cache_path) &&
    ($cache_life == false || filemtime($cache_path) > strtotime($cache_life)) &&
    @$_SERVER['HTTP_CACHE_CONTROL'] != 'no-cache'
)
{
    header('Content-type: image/jpeg');
    header('Location: ' . $cache_uri . $cache);
    exit();
}
else
{
    if (!file_exists($src))
    {
        $src = $document_path.$src;
    }

    $thumb = PhpThumbFactory::create($src);
    $thumb->setOptions($options);
    $thumb->adaptiveResize($w, $h);

    $thumb->save($cache_path);
}

$thumb->show();
flush();