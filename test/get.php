<?php

require_once('../vendor/autoload.php');
require_once('../Library/Creolife/Webscraper.php');

$scraper = new \creoLIFE\Webscraper();

echo "<pre>";

$value = $scraper->parse('http://www.computerbild.de/download/Avast-Free-Antivirus-2015-8482.html',
    array(
        'block'     => 'div[class=descriptionBox user] table tr',
        'blockText' => 'Anzahl Downloads',
        'xpath'     => 'div[class=descriptionBox user] table tr td[2]',
        'valueType' => 'html',
        'toRemove'  => '.',
        'regex'     => ''
    )
);
print_r( $value );

$value = $scraper->parse('http://www.chip.de/downloads/AVG-Free-Antivirus-2015_12996954.html',
    array(
        'block'     => '',
        'blockText' => '',
        'xpath'     => 'div.dl-faktbox div.dl-faktbox-row[3]',
        'valueType' => 'string',
        'toRemove'  => '.',
        'regex'     => '[0-9\.]+'
    )
);
print_r( $value );

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
