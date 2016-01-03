<?php
ini_set('error_reporting', E_ALL); // or error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
date_default_timezone_set('UTC');
header('Content-Type: text/html; charset=utf-8');

require_once(__DIR__ . '/../src/loader.php');

use Creolife\Web\Scraper;

$scraper = new Scraper();



echo "<pre>";

$content = '<div><h2>Test 1</h2></h2><ul><li><a href="http://google.com">Google</a></li></ul>';

$scraper->setContentReplacement(array(
    '&nbsp;' => ' '
));
$scraper->setConfig(
    array(
        'block'         => 'div',
        'blockText'     => 'Test 1',
        'xpath'         => 'div ul li a',
        'attr'          => array('value','href'),
        'valueType'     => 'attr',
        'toRemove'      => '',
        'regex'         => '',
        'elementNumber' => false
    )
);
$lines = $scraper->parseString($content);
print_r( $lines );

