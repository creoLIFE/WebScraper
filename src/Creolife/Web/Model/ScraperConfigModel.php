<?php
/**
 * Created by PhpStorm.
 * User: mirekratman
 * Date: 29/12/15
 * Time: 17:43
 */

namespace Creolife\Web\Model;

class ScraperConfigModel
{
    /**
     * @const string
     */
    const UTF8 = 'UTF-8';

    /**
     * @var string $xpath
     */
    private $xpath;

    /**
     * @var array $contentReplacement
     */
    private $contentReplacement;

    /**
     * @var string $block
     */
    private $block;

    /**
     * @var string $blockText
     */
    private $blockText;

    /**
     * @var array $blockNumber
     */
    private $blockNumber = array();

    /**
     * @var string $valueType
     */
    private $valueType;

    /**
     * @var array $attr
     */
    private $attr;

    /**
     * @var array $toRemove
     */
    private $toRemove;

    /**
     * @var array $elNumber
     */
    private $elNumber;

    /**
     * @var string $regEx
     */
    private $regEx;

    /**
     * @var string $encodeFrom
     */
    private $encodeFrom = self::UTF8;

    /**
     * @var string $encodeTo
     */
    private $encodeTo = self::UTF8;

    /**
     * @return string
     */
    public function getXpath()
    {
        return $this->xpath;
    }

    /**
     * @param string $xpath
     */
    public function setXpath($xpath)
    {
        $this->xpath = $xpath;
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
        $this->contentReplacement = is_array($contentReplacement) ? $contentReplacement : array(0=>$contentReplacement);
    }

    /**
     * @return string
     */
    public function getBlock()
    {
        return $this->block;
    }

    /**
     * @param string $block
     */
    public function setBlock($block)
    {
        $this->block = $block;
    }

    /**
     * @return string
     */
    public function getBlockText()
    {
        return $this->blockText;
    }

    /**
     * @param string $blokText
     */
    public function setBlockText($blockText)
    {
        $this->blockText = $blockText;
    }

    /**
     * @return int
     */
    public function getBlockNumber()
    {
        return $this->blockNumber;
    }

    /**
     * @param int $blockNumber - example: array(1,2,3,4) or array('from'=>1,'to'=>10)
     */
    public function setBlockNumber($blockNumber)
    {
        $this->blockNumber = is_array($blockNumber) ? $blockNumber : array();
    }

    /**
     * @return string
     */
    public function getValueType()
    {
        return $this->valueType;
    }

    /**
     * @param string $valueType
     */
    public function setValueType($valueType)
    {
        $this->valueType = $valueType;
    }

    /**
     * @return array
     */
    public function getAttr()
    {
        return $this->attr;
    }

    /**
     * @param array $attr
     */
    public function setAttr($attr)
    {
        $this->attr = $attr;
    }

    /**
     * @return array
     */
    public function getToRemove()
    {
        return $this->toRemove;
    }

    /**
     * @param array $toRemove
     */
    public function setToRemove($toRemove)
    {
        $this->toRemove = $toRemove;
    }

    /**
     * @return array
     */
    public function getElNumber()
    {
        return $this->elNumber;
    }

    /**
     * @param array $elNumber
     */
    public function setElNumber($elNumber)
    {
        $this->elNumber = $elNumber;
    }

    /**
     * @return string
     */
    public function getRegEx()
    {
        return $this->regEx;
    }

    /**
     * @param string $regEx
     */
    public function setRegEx($regEx)
    {
        $this->regEx = $regEx;
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

}