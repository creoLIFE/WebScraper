<?php
/**
 * creoLIFE Webscraper helping to get some data (numbers,text) from defined website
 * @package Webscraper
 * @author Mirek Ratman
 * @version 1.0.0
 * @since 2014-08-05
 * @license The MIT License (MIT)
 * @copyright 2014 creoLIFE.pl
  Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
  The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/

namespace creoLIFE;

class Webscraper extends \Main_Dom_Parser
{

    /**
    * @var [mixed] $config - type of parser (supported: simple_html_dom, DOMXPath)
    */
    private $config;

    /**
    * Class constructor
    * @method __construct
    */
    public function __construct() {
        parent::__construct('simple_html_dom');
    }

    /**
    * Method will get content from Url
    * @method parse
    * @param [string] $url - url to parse
    * @return [mixed]
    * @todo add custom headers from Main_Http_Headers
    */
    public function getContentFromUrl( $url ) {
        return file_get_contents( $url );
    }

    /**
    * Method will parse url and will get data
    * @method parse
    * @param [string] $url - url to parse
    * @param [string] $xpathConfig - configuration of element to parse.
    * @return [mixed]
    */
    public function parse( $url, $xpathConfig  = array() ) {
        //Set defailts
        $out = new \StdClass;

        //parse
        $content = $this->parser->getHtmlFromString( self::getContentFromUrl($url) );
        $xpathConfig = isset($xpathConfig[0]) ? $xpathConfig : array($xpathConfig);

        foreach( $xpathConfig as $el ){
            $error = null;

            $val = self::getXpath( 
                $content,
                isset($el['xpath']) ? $el['xpath'] : false,
                isset($el['valueType']) ? $el['valueType'] : false,
                isset($el['attr']) ? $el['attr'] : false,
                isset($el['toRemove']) ? $el['toRemove'] : false
            );

            if( $val === '' || $val === null || $val === false ){
                $error = 'Destination element not found';
            }

            $tmpOut = new \StdClass;
            $tmpOut->value = $val;
            $tmpOut->error = $error;
            $tmpOut->xpath = $el['xpath'];

            $out->values[] = $tmpOut;
        }

        $out->url = $url;
        $out->timestamp = time();
        $out->datetime = date( "Y-m-d H:i:s", $out->timestamp );

        return $out;
    }

    /**
    * Method will get value from DOM
    * @method getXpath
    * @param [mixed] $dom - DOM object
    * @param [string] $xpath - path to dom element
    * @param [string] $valueType - type of value to return
    * @param [string] $attr - attribute to read
    * @param [mixed] $toRemove - elements to be removed
    * @return [mixed]
    */
    private function getXpath( $dom, $xpath = null, $valueType = 'string', $attr = null, $toRemove = array() ) {
        //Set defaiults
        $error = null;

        if( empty($valueType) ){
            $valueType = 'string';
        }

        if( empty($xpath) ){
            return null;
        }

        $domEl = $dom->find( $xpath,0 );

        switch( $valueType ){
            case 'dom':
                $val = $domEl;
            break;
            case 'html':
                $val = self::getString( @$domEl->outertext, $toRemove );
            break;
            case 'tag':
                $val = self::getString( @$domEl->tag, $toRemove );
            break;
            case 'attr':
                $val = self::getString( @$domEl->attr[$attr], $toRemove );
            break;
            case 'float':
                $val = self::getFloat( @$domEl->plaintext, $toRemove );
            break;
            case 'integer':
            case 'int':
                $val = self::getInt( @$domEl->plaintext, $toRemove );
            break;
            case 'text':
            default:
                $val = self::getString( @$domEl->plaintext, $toRemove );
            break;
        }

        return $val;
    }

    /**
    * Method will return integer value
    * @method getInt
    * @param [string] $value
    * @param [mixed] $toRemove - elements to be removed
    * @return [int]
    */
    private function getInt( $value, $toRemove ) {
        return (int)str_replace($toRemove,'',$value);
    }

    /**
    * Method will return string value
    * @method getString
    * @param [string] $value
    * @param [mixed] $toRemove - elements to be removed
    * @return [string]
    */
    private function getString( $value, $toRemove ) {
        return (string)str_replace($toRemove,'',$value);
    }

    /**
    * Method will return float value
    * @method getFloat
    * @param [string] $value
    * @param [mixed] $toRemove - elements to be removed
    * @return [float]
    */
    private function getFloat( $value, $toRemove ) {
        return (float)str_replace($toRemove,'',$value);
    }

}