<?php
/**
 * creoLIFE Webscraper helping to get some data (numbers,text) from defined website
 * @package Webscraper
 * @author Mirek Ratman
 * @version 1.0.6
 * @since 2014-08-05
 * @license The MIT License (MIT)
 * @copyright 2014 creoLIFE.pl
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

namespace Creolife\Web;

use Creolife\Web\Model\ScraperModel;
use Creolife\Web\Model\ScraperElementModel;
use Creolife\Web\Model\ScraperConfigModel;

class Scraper extends \Main_Dom_Parser
{

    /**
     * @var array $config
     */
    protected $config;

    /**
     * @return mixed
     */
    public function getParser()
    {
        return $this->parser;
    }

    /**
     * @param mixed $parser
     */
    public function setParser($parser)
    {
        $this->parser = $parser;
    }

    /**
     * @return ScraperConfigModel
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param ScraperConfigModel $config
     */
    public function setConfig(ScraperConfigModel $config)
    {
        $this->config = $config;
    }

    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct('simple_html_dom');
        $this->setConfig(new ScraperConfigModel());
    }

    /**
     * Method will parse url and will get data
     * @param string $url - url to parse
     * @return ScraperModel
     * @todo add custom headers from Main_Http_Headers
     */
    public function parseUrl($url)
    {
        $out = @file_get_contents($url, FILE_USE_INCLUDE_PATH);
        return self::parseString($out, $url);
    }

    /**
     * Method will parse url and will get data
     * @param string $string - string to parse
     * @return ScraperModel
     */
    public function parseString($string, $url = null)
    {
        $scraperModel = new ScraperModel();

        $scraperModel->setUrl($url);
        $scraperModel->setTimestamp(time());

        //get content
        $content = $this->parser->getHtmlFromString($string, true, true, DEFAULT_TARGET_CHARSET, true, DEFAULT_BR_TEXT, DEFAULT_SPAN_TEXT, 1000000);

        if ($content) {
            $scraperElementModel = $this->parseContent($content);
            //Check if parsed content exists and return data or error
            if ($scraperElementModel->getValues()) {
                $scraperModel->setResult($scraperElementModel);
            } else {
                $scraperModel->setError('Cannot load values defined by xPath');
            }
            //If content not exists set error
        } else {
            $scraperModel->setError('Cannot load content');
        }

        return $scraperModel;
    }


    /**
     * Method will parse url and will get data
     * @param string $content - read content
     * @return ScraperElementModel
     */
    private function parseContent($content)
    {
        $scraperElementModel = new ScraperElementModel();
        $config = $this->getConfig();

        $scraperElementModel->setXpath($config->getXpath());
        $scraperElementModel->setBlockXpath($config->getBlock());

        $values = $this->getParsedValuesFromContent($content);

        if ($values) {
            $scraperElementModel->setValues($values);
        } else {
            $scraperElementModel->setError('Could not parse xPath Element');
        }

        return $scraperElementModel;
    }

    /**
     * Method will get value from DOM by xPath
     * @param mixed $dom
     * @param ScraperConfigModel $config
     * @return mixed
     */
    //, $xpath = null, $valueType = 'string', array $attr = array(), $toRemove = null, $elOnList = null, $elOnListText = null, $elOnListNumber = false, $regex = null, $specifiedEl = 0)
    private function getParsedValuesFromContent($dom)
    {
        //Set defaiults
        $error = null;
        $val = null;
        $config = $this->getConfig();

        //If $xpath is not defined stop and return null
        if (null === $config->getXpath()) {
            return null;
        }

        if (null !== $config->getBlock() || count($config->getBlockNumber())) {
            $domElements = $this->getDomElementsFromBlock($dom);
        } else {
            $domElements = $this->doDomFind($dom);
        }

        foreach ($domElements as $el) {
            switch ($config->getValueType()) {

                case 'dom':
                    $val[] = $domElements;
                    break;

                case 'html':
                    $val[] = $this->getEncodedValue(
                        self::getString(
                            self::applyRegex(@$el->outertext, $config->getRegex()),
                            $config->getToRemove()
                        ),
                        $config
                    );
                    break;

                case 'tag':
                    $val[] = $this->getEncodedValue(
                        self::getString(
                            self::applyRegex(@$el->tag, $config->getRegex()),
                            $config->getToRemove()
                        ),
                        $config
                    );
                    break;

                case 'attr':
                    $a = array();
                    foreach ($config->getAttr() as $attribute) {
                        $attrVal = $this->getEncodedValue(
                            self::getString(
                                self::applyRegex(@$el->attr[$attribute], $config->getRegex()),
                                $config->getToRemove()
                            ),
                            $config
                        );
                        $value = $this->getEncodedValue(
                            self::getString(
                                self::applyRegex(@$el->plaintext, $config->getRegex()),
                                $config->getToRemove()
                            ),
                            $config
                        );

                        $a[$attribute] = $attribute === 'value' ? $value : $attrVal;
                    }
                    $val[] = $a;
                    break;

                case 'float':
                    $val[] = $this->getEncodedValue(
                        self::getFloat(
                            self::applyRegex(@$el->plaintext, $config->getRegex()),
                            $config->getToRemove()
                        ),
                        $config
                    );
                    break;

                case 'integer':
                case 'int':
                    $val[] = $this->getEncodedValue(
                        self::getInt(
                            self::applyRegex(@$el->plaintext, $config->getRegex()),
                            $config->getToRemove()
                        ),
                        $config
                    );
                    break;
                case 'text':
                default:
                    $val[] = $this->getEncodedValue(
                        self::getString(
                            self::applyRegex(@$el->plaintext, $config->getRegex()),
                            $config->getToRemove()
                        ),
                        $config
                    );
                    break;
            }
        }

        return $val;
    }

    /**
     * Method will get Dom Elements from Block
     * @param mixed $dom
     * @return array|mixed
     */
    private function getDomElementsFromBlock($dom)
    {
        $domElements = array();
        $config = $this->getConfig();

        if (null !== $config->getBlockText()) {
            $domElements = $this->getDomElementsFromBlockByText($dom);
        } elseif (null !== $config->getBlockNumber()) {
            $domElements = $this->getDomElementsFromBlockByNumber($dom);
        }

        return $domElements;
    }

    /**
     * Method will search for defined block and if it will be found will process elements matching
     * @param mixed $dom
     * @return array|mixed
     */
    private function getDomElementsFromBlockByText($dom)
    {
        $out = array();
        $config = $this->getConfig();
        $domBlockElements = self::doDomBlockFind($dom);

        foreach ($domBlockElements as $element) {
            //Replace content elements for block text matching
            $content = $config->getContentReplacement() ? strtr($element->plaintext, $config->getContentReplacement()) : $element->plaintext;

            //Find block
            $prepareExp = '/' . strip_tags($config->getBlockText()) . '/';

            //Do matching
            preg_match($prepareExp, $content, $matches);
            if (isset($matches[0]) && $matches[0] == strip_tags($config->getBlockText())) {
                $elXpath = trim(str_replace($config->getBlock(), '', $config->getXpath()));
                $out = $element->find($elXpath);
            }
        }

        return $out;
    }


    /**
     * Method will get Elements from Block using block number
     * @param mixed $dom
     * @return array|mixed
     */
    private function getDomElementsFromBlockByNumber($dom)
    {
        $config = $this->getConfig();
        $blockNumber = $config->getBlockNumber();
        $domBlockElements = self::doDomBlockFind($dom);
        $blocks = self::getBlocksByNumber($domBlockElements, $blockNumber);
        $out = [];

        foreach ($blocks as $block) {
            if ($block) {
                $elXpath = trim(str_replace($config->getBlock(), '', $config->getXpath()));
                $out = array_merge($out, $block->find($elXpath));
            }
        }
        return $out;
    }

    /**
     * Method will process DOM searching by Xpath
     * @param mixed $domBlockElements
     * @param integer $blockNumber
     * @return array|mixed
     */
    private function getBlocksByNumber($domBlockElements, $blockNumber)
    {
        $out = null;
        if (isset($blockNumber['from']) && !isset($blockNumber['to'])) {
            return array_slice($domBlockElements, (int)$blockNumber['from']);
        }
        if (!isset($blockNumber['from']) && isset($blockNumber['to'])) {
            return array_slice($domBlockElements, 0, (int)$blockNumber['to']);
        }
        if (isset($blockNumber['from']) && isset($blockNumber['to'])) {
            return array_slice($domBlockElements, (int)$blockNumber['from'], (int)$blockNumber['to']);
        }
        if (is_array($blockNumber) && count($blockNumber) && !isset($blockNumber['from']) && !isset($blockNumber['to'])) {
            foreach ($blockNumber as $block) {
                $out[$block] = $domBlockElements[$block];
            }
            return $out;
        }
        if (is_array($blockNumber) && !count($blockNumber) && !isset($blockNumber['from']) && !isset($blockNumber['to'])) {
            return array_intersect($domBlockElements, $blockNumber);
        }

        return [];
    }

    /**
     * Method will process DOM searching by Xpath
     * @param mixed $dom
     * @return array|mixed
     */
    private function doDomFind($dom)
    {
        $config = $this->getConfig();
        return $dom->find($config->getXpath());
    }

    /**
     * Method will process DOM searching by Block Xpath
     * @param mixed $dom
     * @return array|mixed
     */
    private function doDomBlockFind($dom)
    {
        $config = $this->config;
        return $dom->find($config->getBlock());
    }

    /**
     * Method will apply regular expression on taken value
     * @param string $value
     * @param mixed $regex - regular expression to apply
     * @return integer
     */
    private function applyRegex($value, $regex)
    {
        if (!empty($regex)) {
            preg_match('/' . $regex . '/', $value, $matches);
            return isset($matches[0]) ? $matches[0] : $value;
        }
        return $value;
    }

    /**
     * Method will return integer value
     * @param string $value
     * @param mixed $toRemove - elements to be removed
     * @return integer
     */
    private function getInt($value, $toRemove)
    {
        return (int)str_replace($toRemove, '', filter_var($value, FILTER_SANITIZE_NUMBER_INT));
    }

    /**
     * Method will return string value
     * @param string $value
     * @param mixed $toRemove - elements to be removed
     * @return string
     */
    private function getString($value, $toRemove)
    {
        return (string)str_replace($toRemove, '', $value);
    }

    /**
     * Method will return float value
     * @param string $value
     * @param mixed $toRemove - elements to be removed
     * @return float
     */
    private function getFloat($value, $toRemove)
    {
        return (float)str_replace($toRemove, '', $value);
    }

    /**
     * Method will return encoded value
     * @param string $value
     * @param ScraperConfigModel $config
     * @return string
     */
    private function getEncodedValue($value, ScraperConfigModel $config)
    {
        if ($config->getEncodeFrom() !== ScraperConfigModel::UTF8 || $config->getEncodeTo() !== ScraperConfigModel::UTF8) {
            return mb_convert_encoding($value, $config->getEncodeFrom(), $config->getEncodeTo());
        }
        return $value;
    }

}
