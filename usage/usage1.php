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

$scraper->setContentReplacement(array(
    '&nbsp;' => ' '
));
$scraper->setConfig(
    array(
        'block'         => 'table[class=table table-bordered table-schedule]',
        'blockText'     => 'Tramwaj normalny',
        'xpath'         => 'tbody tr a.btn',
        'attr'          => array('value','href'),
        'valueType'     => 'attr',
        'toRemove'      => '',
        'regex'         => '',
        'elementNumber' => false
    )
);
$lines = $scraper->parseUrl('http://www.wroclaw.pl/rozklady-jazdy');
print_r( $lines );

