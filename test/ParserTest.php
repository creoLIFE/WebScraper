<?php
ini_set('error_reporting', E_ALL); // or error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
date_default_timezone_set('UTC');

use Creolife\Web\Scraper;
use Creolife\Web\Model\ScraperConfigModel;

class HelpersTest extends PHPUnit_Framework_TestCase
{
    private $scraper;
    private $config;
    private $content =
        '
         <div>
            <h2>Test 1</h2>
            <ul>
                <li><a href="http://google.com">Google</a></li>
                <li><a href="http://yahoo.com">Yahoo</a></li>
                <li><a href="http://msn.com">MSN</a></li>
            </ul>
         </div>
         <div>
            <h2>Test 2</h2>
            <ul>
                <li><a href="http://amazon.com">Amazon</a></li>
                <li><a href="http://facebook.com">Facebook</a></li>
            </ul>
         </div>
         ';

    private $contentBroken =
        '<div>
            <h2>Test 1</h2></h2>
            <ul>
                <li><a href="http://google.com">Google</a></li>
                <a href="http://yahoo.com">Yahoo</a></li>
                <li><a href="http://msn.com">MSN</a></li>
         </div>';

    public function __construct()
    {
        $this->scraper = new Scraper();
        $this->config = new ScraperConfigModel();
    }

    public function testBasicParse()
    {
        $config = $this->config;

        $config->setXpath('div ul li a');
        $config->setValueType('string');
        $this->scraper->setConfig($config);

        //Process parse
        $parsed = $this->scraper->parseString($this->content);

        //process test
        $this->assertEquals('Facebook', $parsed->getResult()->getValues()[4]);
    }

    public function testDetailedParse()
    {
        $config = $this->config;

        $config->setBlock('div');
        $config->setBlockText('Test 1');
        $config->setXpath('div ul li a');
        $config->setAttr(array('value', 'href'));
        $config->setValueType('attr');
        $config->setContentReplacement(array('&nbsp;' => ' '));
        $this->scraper->setConfig($config);

        //Process parse
        $parsed = $this->scraper->parseString($this->content);

        //process test
        $this->assertEquals('Google', $parsed->getResult()->getValues()[0]['value']);
    }

    public function testBrokenHtmlParse()
    {
        $config = $this->config;

        $config->setBlock('div');
        $config->setBlockText('Test 1');
        $config->setXpath('div ul li a');
        $config->setAttr(array('value', 'href'));
        $config->setValueType('attr');
        $config->setContentReplacement(array('&nbsp;' => ' '));
        $this->scraper->setConfig($config);

        //Process parse
        $parsed = $this->scraper->parseString($this->contentBroken);

        //process test
        $this->assertEquals('Google', $parsed->getResult()->getValues()[0]['value']);
    }

}
