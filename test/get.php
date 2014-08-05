<?php

require_once('../vendor/autoload.php');
require_once('../Library/Creolife/Webscraper.php');

$scraper = new \creoLIFE\Webscraper();

echo "<pre>";

$value = $scraper->parse('http://www.creolife.pl',
    array(
        array(
            'xpath'     => 'div.contact',
            'valueType' => 'html'
        ),
        array(
            'xpath'     => 'div.contact p',
            'valueType' => 'text'
        )
    )
);
print_r( $value );


$value = $scraper->parse('http://www.creolife.pl',
    array(
        'xpath'     => 'div.box p.message',
        'valueType' => 'text',
        'toRemove'  => '?'
    )
);
print_r( $value );


$value = $scraper->parse('http://www.creolife.pl',
    array(
        'xpath'     => 'div.boxFirst p.links a',
        'valueType' => 'html',
        'toRemove'  => array('(',')')
    )
);
print_r( $value );

