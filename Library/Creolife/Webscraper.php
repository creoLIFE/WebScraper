<?php
/**
 * creoLIFE Webscraper helping to get some data (numbers,text) from defined website
 * @package Webscraper
 * @author Mirek Ratman
 * @version 1.0.6
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
    */
    public function __construct() {
        parent::__construct('simple_html_dom');
    }

    /**
    * Method will get content from Url
    * @param [string] $url - url to parse
    * @return [mixed]
    * @todo add custom headers from Main_Http_Headers
    */
    public function getContentFromUrl( $url ) {
        $out = @file_get_contents( $url, FILE_USE_INCLUDE_PATH );
        return $out ? $out : false;
    }

    /**
    * Method will parse url and will get data
    * @param [string] $url - url to parse
    * @param [string] $xpathConfig - configuration of element to parse.
    * @return [mixed]
    */
    public function parse( $url, $xpathConfig  = array() ) {
        //Set defailts
        $out = new \StdClass;

        $out->url = $url;
        $out->error = '';
        $out->timestamp = time();
        $out->datetime = date( "Y-m-d H:i:s", $out->timestamp );

        //parse
        $content = $this->parser->getHtmlFromString( self::getContentFromUrl($url) );
        if( $content ){
            $xpathConfig = isset($xpathConfig[0]) ? $xpathConfig : array($xpathConfig);

            foreach( $xpathConfig as $el ){
                $error = null;

                $val = self::getXpath( 
                    $content,
                    isset($el['xpath']) ? $el['xpath'] : null,
                    isset($el['valueType']) ? $el['valueType'] : null,
                    isset($el['attr']) ? $el['attr'] : null,
                    isset($el['toRemove']) ? $el['toRemove'] : null,
                    isset($el['block']) ? $el['block'] : null,
                    isset($el['blockText']) ? $el['blockText'] : null,
                    isset($el['regex']) ? $el['regex'] : null
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
        }
        else{
            $tmpOut = new \StdClass;
            $tmpOut->value = 0;
            $tmpOut->error = 'Cannot load xPath';
            $tmpOut->xpath = '';
            $out->values[] = $tmpOut;

            $out->error = 'Cannot load URL';
        }

        return $out;
    }

    /**
    * Method will get value from DOM by xPath
    * @param [mixed] $dom - DOM object
    * @param [string] $xpath - path to dom element
    * @param [string] $valueType - type of value to return
    * @param [string] $attr - attribute to read
    * @param [mixed] $toRemove - elements to be removed
    * @param [string] $elOnList - Block element on list of results (when there is more numbers with specified path)
    * @param [string] $elOnListText - text which will define block element on list (when there is more numbers with specified path)
    * @param [string] $regex - regular expression to apply
    * @return [mixed]
    */
    private function getXpath( $dom, $xpath = null, $valueType = null, $attr = null, $toRemove = null, $elOnList = null, $elOnListText = null, $regex = null ) {
        //Set defaiults
        $error = null;
        $val = null;

        if( empty($valueType) ){
            $valueType = 'string';
        }

        if( empty($xpath) ){
            return null;
        }

        if( $dom ){
            $domEl = $dom->find( $xpath,0 );

            if( !empty($elOnList) && !empty($elOnListText) ){
                $domElList = $dom->find( $elOnList );
                
                foreach( $domElList as $key=>$d ){
                    preg_match('/' . strip_tags($elOnListText) . '/', $d->outertext, $matches );
                    if( isset($matches[0]) && $matches[0] == strip_tags($elOnListText) ){
                        $domEl = $d->find( str_replace($elOnList, '', $xpath),0 );
//                        debug($d->find( str_replace($elOnList, '', $xpath),0 )->innertext );
                    }
                    
                }
            }

            switch( $valueType ){
                case 'dom':
                    $val = $domEl;
                break;
                case 'html':
                    $val = self::getString( self::applyRegex(@$domEl->outertext, $regex), $toRemove );
                break;
                case 'tag':
                    $val = self::getString( self::applyRegex(@$domEl->tag, $regex), $toRemove );
                break;
                case 'attr':
                    $val = self::getString( self::applyRegex(@$domEl->attr[$attr], $regex), $toRemove );
                break;
                case 'float':
                    $val = self::getFloat( self::applyRegex(@$domEl->plaintext, $regex), $toRemove );
                break;
                case 'integer':
                case 'int':
                    $val = self::getInt( self::applyRegex(@$domEl->plaintext, $regex), $toRemove );
                break;
                case 'text':
                default:
                    $val = self::getString( self::applyRegex(@$domEl->plaintext, $regex), $toRemove );
                break;
            }
        }

        return $val;
    }

    /**
    * Method will apply regular expression on taken value
    * @param [string] $value
    * @param [mixed] $regex - regular expression to apply
    * @return [int]
    */
    private function applyRegex( $value, $regex ) {
        if( !empty($regex) ){
            preg_match('/' . $regex . '/', $value, $matches);
            return isset($matches[0]) ? $matches[0] : $value;
        }
        return $value;
    }

    /**
    * Method will return integer value
    * @param [string] $value
    * @param [mixed] $toRemove - elements to be removed
    * @return [int]
    */
    private function getInt( $value, $toRemove ) {
        return (int)str_replace($toRemove,'',filter_var($value, FILTER_SANITIZE_NUMBER_INT) );
        //return (int)str_replace($toRemove,'',$value);
    }

    /**
    * Method will return string value
    * @param [string] $value
    * @param [mixed] $toRemove - elements to be removed
    * @return [string]
    */
    private function getString( $value, $toRemove ) {
        return (string)str_replace($toRemove,'',$value);
    }

    /**
    * Method will return float value
    * @param [string] $value
    * @param [mixed] $toRemove - elements to be removed
    * @return [float]
    */
    private function getFloat( $value, $toRemove ) {
        return (float)str_replace($toRemove,'',$value);
    }

}