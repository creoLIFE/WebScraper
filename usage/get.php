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
$scraper->setConfig(array(
    'block'         => 'div',
    'blockText'     => 'Test 2',
    'xpath'         => 'div ul li a',
    'valueType'     => 'attr',
    'attr'          => array('href','value','class','data-type'),
    'toRemove'      => '',
    'regex'         => '',
    'elementNumber' => 1
));

$values = $scraper->parse('http://github.local/WebScraper/usage/pages/test1.html');
print_r( $values );

echo "-------------------------------------------------------------<br>";

$scraper->setConfig(array(
    'block'         => 'div',
    'blockText'     => 'Test 2',
    'xpath'         => 'div ul li a',
    'valueType'     => 'attr',
    'attr'          => array('href','value','class','data-type'),
    'toRemove'      => '',
    'regex'         => '',
    'elementNumber' => false
));

$values = $scraper->parse('http://github.local/WebScraper/usage/pages/test1.html');
print_r( $values );

echo "-------------------------------------------------------------<br>";

$scraper->setConfig(array(
        'block'     => 'div[class=descriptionBox user] table tr',
        'blockText' => 'Anzahl Downloads',
        'xpath'     => 'div[class=descriptionBox user] table tr td[2]',
        'valueType' => 'html',
        'toRemove'  => '.',
        'regex'     => ''
    )
);
$values = $scraper->parse('http://www.computerbild.de/download/Avast-Free-Antivirus-2015-8482.html');
print_r( $values );

echo "-------------------------------------------------------------<br>";

$scraper->setConfig(array(
        'block'     => '',
        'blockText' => '',
        'xpath'     => 'div.dl-faktbox div.dl-faktbox-row[3]',
        'valueType' => 'string',
        'toRemove'  => '.',
        'regex'     => '[0-9\.]+'
    )
);
$values = $scraper->parse('http://www.chip.de/downloads/AVG-Free-Antivirus-2015_12996954.html');
print_r( $values );

echo "-------------------------------------------------------------<br>";

$scraper->setConfig(array(
        'xpath'     => 'div.contact',
        'valueType' => 'html'
    )
);
$values = $scraper->parse('http://www.creolife.pl');
print_r( $values );

echo "-------------------------------------------------------------<br>";

$scraper->setConfig(array(
        'xpath'     => 'div.box p.message',
        'valueType' => 'text',
        'toRemove'  => '?'
    )
);
$values = $scraper->parse('http://www.creolife.pl');
print_r( $values );

echo "-------------------------------------------------------------<br>";

$scraper->setConfig(array(
        'xpath'     => 'div.boxFirst p.links a',
        'valueType' => 'html',
        'toRemove'  => array('(',')')
    )
);
$values = $scraper->parse('http://www.creolife.pl');
print_r( $values );
