<?php
ini_set('error_reporting', E_ALL); // or error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
date_default_timezone_set('UTC');
header('Content-Type: text/html; charset=utf-8');

require_once(__DIR__ . '/../src/loader.php');

use Creolife\Web\Scraper;
use Creolife\Web\Model\ScraperConfigModel;

//Instances
$scraper = new Scraper();
$scraperConfigModel = new ScraperConfigModel();


//Content to parse
$content = '<div><h2>Test 1</h2></h2><ul>
    <li><a href="http://google.com">Google</a></li>
    <li><a href="http://yahoo.com">Yahoo</a></li>
    <li><a href="http://msn.com">MSN</a></li>
</ul>';


//Parser config
$scraperConfigModel->setBlock('div');
$scraperConfigModel->setBlockText('Test 1');
$scraperConfigModel->setXpath('div ul li a');
$scraperConfigModel->setAttr(array('value','href'));
$scraperConfigModel->setValueType('attr');
$scraperConfigModel->setContentReplacement(array('&nbsp;' => ' '));
$scraper->setConfig($scraperConfigModel);

//Process parse
$parsed = $scraper->parseString($content);

//Print data
echo "<pre>";
print_r( $parsed );

