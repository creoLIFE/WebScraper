<?php
ini_set('error_reporting', E_ALL); // or error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
date_default_timezone_set('UTC');
header('Content-Type: text/html; charset=utf-8');

require_once(__DIR__ . '/../src/loader.php');

use Creolife\Web\Scraper;
use Creolife\Web\Model\ScraperConfigModel;

$scraper = new Scraper();



echo "<pre>";

$scraperConfigModel = new ScraperConfigModel();
$scraperConfigModel->setBlock('div');
$scraperConfigModel->setBlockText('Test 2');
$scraperConfigModel->setXpath('div ul li a');
$scraperConfigModel->setAttr(array('href','value','class','data-type'));
$scraperConfigModel->setValueType('attr');
$scraperConfigModel->setBlockNumber(1);
$scraperConfigModel->setContentReplacement(array('&nbsp;' => ' '));
$scraper->setConfig($scraperConfigModel);

$values = $scraper->parseUrl('http://github.local/WebScraper/usage/pages/test1.html');
print_r( $values );

echo "-------------------------------------------------------------<br>";

$scraperConfigModel = new ScraperConfigModel();
$scraperConfigModel->setBlock('div');
$scraperConfigModel->setBlockText('Test 2');
$scraperConfigModel->setXpath('div ul li a');
$scraperConfigModel->setAttr(array('href','value','class','data-type'));
$scraperConfigModel->setValueType('attr');
$scraperConfigModel->setContentReplacement(array('&nbsp;' => ' '));
$scraper->setConfig($scraperConfigModel);

$values = $scraper->parseUrl('http://github.local/WebScraper/usage/pages/test1.html');
print_r( $values );

echo "-------------------------------------------------------------<br>";
/*
$scraperConfigModel = new ScraperConfigModel();
$scraperConfigModel->setBlock('div[class=descriptionBox user] table tr');
$scraperConfigModel->setBlockText('Anzahl Downloads');
$scraperConfigModel->setXpath('div[class=descriptionBox user] table tr td[2]');
$scraperConfigModel->setValueType('html');
$scraper->setConfig($scraperConfigModel);

$values = $scraper->parseUrl('http://www.computerbild.de/download/Avast-Free-Antivirus-2015-8482.html');
print_r( $values );
*/
echo "-------------------------------------------------------------<br>";
$scraperConfigModel = new ScraperConfigModel();
$scraperConfigModel->setXpath('.Dl-Factbox tr[1]');
$scraperConfigModel->setValueType('html');
//$scraperConfigModel->setToRemove('.');
//$scraperConfigModel->setRegEx('[0-9\.]+');
$scraper->setConfig($scraperConfigModel);

print_r('------');
$values = $scraper->parseUrl('http://www.chip.de/downloads/AVG-Free-Antivirus-2015_12996954.html');
print_r( $values );

echo "-------------------------------------------------------------<br>";
$scraperConfigModel = new ScraperConfigModel();
$scraperConfigModel->setXpath('div.contact');
$scraperConfigModel->setValueType('html');
$scraper->setConfig($scraperConfigModel);

print_r('------');
$values = $scraper->parseUrl('http://www.creolife.pl');
print_r( $values );

echo "-------------------------------------------------------------<br>";
$scraperConfigModel = new ScraperConfigModel();
$scraperConfigModel->setXpath('div.box p.message');
$scraperConfigModel->setValueType('text');
$scraperConfigModel->setToRemove('?');
$scraper->setConfig($scraperConfigModel);

$values = $scraper->parseUrl('http://www.creolife.pl');
print_r( $values );

echo "-------------------------------------------------------------<br>";
$scraperConfigModel = new ScraperConfigModel();
$scraperConfigModel->setXpath('div.boxFirst p.links a');
$scraperConfigModel->setValueType('html');
$scraperConfigModel->setToRemove(array('(',')'));
$scraper->setConfig($scraperConfigModel);

$values = $scraper->parseUrl('http://www.creolife.pl');
print_r( $values );
