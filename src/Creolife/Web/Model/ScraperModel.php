<?php
/**
 * Created by PhpStorm.
 * User: mirekratman
 * Date: 29/12/15
 * Time: 17:43
 */

namespace Creolife\Web\Model;

class ScraperModel {

    /**
     * @var string $url
     */
    private $url;

    /**
     * @var string $error
     */
    private $error;

    /**
     * @var string $timestamp
     */
    private $timestamp;

    /**
     * @var ScraperElementModel $result
     */
    private $result;

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param mixed $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
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
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @param string $timestamp
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;
    }

    /**
     * @return ScraperElementModel
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @param ScraperElementModel $result
     */
    public function setResult(ScraperElementModel $result)
    {
        $this->result = $result;
    }

}