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

class Scraper extends \Main_Dom_Parser
{

    /**
     * @const string
     */
    const UTF8 = 'UTF-8';

    /**
     * @var string $url
     */
    private $config = array();

    /**
     * @var string $encodeFrom
     */
    private $encodeFrom = self::UTF8;

    /**
     * @var string $encodeTo
     */
    private $encodeTo = self::UTF8;

    /**
     * @var array $contentReplacement
     */
    private $contentReplacement = array();

    /**
     * @return string
     * @param string $key
     */
    public function getConfig($key = null)
    {
        return $key && isset($this->config[$key]) ? $this->config[$key] : null;
    }

    /**
     * @param string $config - configuration of element to parse. Multiple configuration as array.
     *                      array(
     *                          'block' => 'table[class=table table-bordered table-schedule]',
     *                          'blockText' => 'Some text which define block',
     *                          'blockNumber' => 1,
     *                          'xpath' => 'tbody tr a.btn',
     *                          'valueType' => 'text',
     *                          'toRemove' => '',
     *                          'regex' => ''
     *                          'elementNumber' => 0
     *                      )
     */
    public function setConfig($config)
    {
        $c = array(
            'xpath' => isset($config['xpath']) ? $config['xpath'] : null,
            'valueType' => isset($config['valueType']) ? $config['valueType'] : null,
            'attr' => isset($config['attr']) ? $config['attr'] : array(),
            'toRemove' => isset($config['toRemove']) ? $config['toRemove'] : null,
            'block' => isset($config['block']) ? $config['block'] : null,
            'blockText' => isset($config['blockText']) ? $config['blockText'] : null,
            'blockNumber' => isset($config['blockNumber']) ? $config['blockNumber'] : false,
            'regex' => isset($config['regex']) ? $config['regex'] : null,
            'elementNumber' => isset($config['elementNumber']) ? $config['elementNumber'] : false
        );

        $this->config = $c;
    }

    /**
     * @return string
     */
    public function getEncodeFrom()
    {
        return $this->encodeFrom;
    }

    /**
     * @param string $encodeFrom
     */
    public function setEncodeFrom($encodeFrom)
    {
        $this->encodeFrom = $encodeFrom;
    }

    /**
     * @return string
     */
    public function getEncodeTo()
    {
        return $this->encodeTo;
    }

    /**
     * @param string $encodeTo
     */
    public function setEncodeTo($encodeTo)
    {
        $this->encodeTo = $encodeTo;
    }

    /**
     * @return array
     */
    public function getContentReplacement()
    {
        return $this->contentReplacement;
    }

    /**
     * @param array $contentReplacement
     */
    public function setContentReplacement($contentReplacement)
    {
        $this->contentReplacement = $contentReplacement;
    }


    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct('simple_html_dom');
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
        $content = $this->parser->getHtmlFromString($string);

        if ($content) {
            $scraperElementModel = $this->parseContent($content);

            //Check if parsed content exists and return data or error
            if ($scraperElementModel->getValues()) {
                $scraperModel->setValues($scraperElementModel);
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
        $scraperElementModel->setXpath($this->getConfig('xpath'));
        $scraperElementModel->setBlockXpath($this->getConfig('block'));

        $values = $this->getXpathValues(
            $content,
            $this->getConfig('xpath'),
            $this->getConfig('valueType'),
            $this->getConfig('attr'),
            $this->getConfig('toRemove'),
            $this->getConfig('block'),
            $this->getConfig('blockText'),
            $this->getConfig('blockNumber'),
            $this->getConfig('regex'),
            $this->getConfig('elementNumber')
        );

        if ($values) {
            $scraperElementModel->setValues($values);
        } else {
            $scraperElementModel->setError('Could not parse xPath Element');
        }

        return $scraperElementModel;
    }

    /**
     * Method will get value from DOM by xPath
     * @param mixed $dom - DOM object
     * @param string $xpath - path to dom element
     * @param string $valueType - type of value to return
     * @param string $attr - attribute to read
     * @param mixed $toRemove - elements to be removed
     * @param string $elOnList - Block element on list of results (when there is more numbers with specified path)
     * @param string $elOnListText - text which will define block element on list (when there is more numbers with specified path)
     * @param string $regex - regular expression to apply
     * @param integer|boolean $specifiedEl - specify an element that will be returned when more elements will be found. Boolean false will return all found elements
     * @return mixed
     */
    private function getXpathValues($dom, $xpath = null, $valueType = 'string', array $attr = array(), $toRemove = null, $elOnList = null, $elOnListText = null, $elOnListNumber = false, $regex = null, $specifiedEl = 0)
    {
        //Set defaiults
        $error = null;
        $val = null;

        //If $xpath is not defined stop and return null
        if (empty($xpath)) {
            return null;
        }

        $domEl = $specifiedEl === false ? $dom->find($xpath) : $dom->find($xpath, $specifiedEl);

        if (!empty($elOnList) && !empty($elOnListText)) {
            $domElList = $dom->find($elOnList);

            foreach ($domElList as $key => $d) {
                $prepareExp = '/' . strip_tags($elOnListText) . '/';
                $content = strtr($d->outertext, $this->getContentReplacement());
                preg_match($prepareExp, $content, $matches);

                if (isset($matches[0]) && $matches[0] == strip_tags($elOnListText)) {
                    $domEl = $specifiedEl === false ? $d->find(str_replace($elOnList, '', $xpath)) : $d->find(str_replace($elOnList, '', $xpath), $specifiedEl);
                }
            }
        }

        //Temp fix for single elements
        if ($specifiedEl !== false) {
            $domEl = array(0 => $domEl);
        }

        foreach ($domEl as $el) {
            switch ($valueType) {
                case 'dom':
                    $val[] = $domEl;
                    break;
                case 'html':
                    $val[] = $this->getEncodedValue(self::getString(self::applyRegex(@$el->outertext, $regex), $toRemove));
                    break;
                case 'tag':
                    $val[] = $this->getEncodedValue(self::getString(self::applyRegex(@$el->tag, $regex), $toRemove));
                    break;
                case 'attr':
                    $a = array();
                    foreach ($attr as $attribute) {
                        $attrVal = $this->getEncodedValue(self::getString(self::applyRegex(@$el->attr[$attribute], $regex), $toRemove));
                        $value = $this->getEncodedValue(self::getString(self::applyRegex(@$el->plaintext, $regex), $toRemove));
                        $a[$attribute] = $attribute === 'value' ? $value : $attrVal;
                    }
                    $val[] = $a;
                    break;
                case 'float':
                    $val[] = $this->getEncodedValue(self::getFloat(self::applyRegex(@$el->plaintext, $regex), $toRemove));
                    break;
                case 'integer':
                case 'int':
                    $val[] = $this->getEncodedValue(self::getInt(self::applyRegex(@$el->plaintext, $regex), $toRemove));
                    break;
                case 'text':
                default:
                    $val[] = $this->getEncodedValue(self::getString(self::applyRegex(@$el->plaintext, $regex), $toRemove));
                    break;
            }
        }

        return $val;
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
     * @return string
     */
    private function getEncodedValue($value)
    {
        if ($this->getEncodeFrom() !== self::UTF8 || $this->getEncodeTo() !== self::UTF8) {
            return mb_convert_encoding($value, $this->getEncodeFrom(), $this->getEncodeTo());
        }
        return $value;
    }

}