<?php

use MultihandED\Regent\Facades\Regent;

test('insertModifiers', function () 
{
    $dataSamples = [
        'i' => array(['i', 'i'], []),
        '-m' => array([], ['m']),
        's' => array(['s', ' s'], [' s ', 's ']),
        's-x' => array(['s'], ['x']),
        'imsx-UJ' => array(['i', 'm', ' s', 'x'], ['s ', 'x', 'U', 'J']),
    ];

    foreach($dataSamples as $result => $args)
    {
        $this->assertSame("(?$result)", Regent::insertModifiers($args[0], $args[1])->pattern);
    }
});