<?php
/**
 * Created by PhpStorm.
 * User: mirekratman
 * Date: 29/12/15
 * Time: 17:29
 */

require_once __DIR__ . '/../vendor/autoload.php';
spl_autoload_register(function ($className) {
    $filename = __DIR__ . '/' . str_replace('\\', '/', $className) . ".php";
    if (file_exists($filename)) {
        include($filename);
        if (class_exists($className)) {
            return true;
        }
    }
    return false;
});