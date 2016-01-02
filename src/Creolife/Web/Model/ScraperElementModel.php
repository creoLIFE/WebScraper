<?php
/**
 * Created by PhpStorm.
 * User: mirekratman
 * Date: 29/12/15
 * Time: 17:43
 */

namespace Creolife\Web\Model;

class ScraperElementModel {

    /**
     * @var string $value
     */
    private $values;

    /**
     * @var string $error
     */
    private $error;

    /**
     * @var string $xpath
     */
    private $xpath;

    /**
     * @var string $blockXpath
     */
    private $blockXpath;

    /**
     * @return string
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * @param string $values
     */
    public function setValues($values)
    {
        $this->values = $values;
    }

    /**
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @param string $error
     */
    public function setError($error)
    {
        $this->error = $error;
    }

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
     * @return string
     */
    public function getBlockXpath()
    {
        return $this->blockXpath;
    }

    /**
     * @param string $blockXpath
     */
    public function setBlockXpath($blockXpath)
    {
        $this->blockXpath = $blockXpath;
    }

}