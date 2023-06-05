<?php

use MultihandED\Regent\Facades\Regent;

test('clearFlags', function()
{
    $regent = Regent::init();
    $regent->flags = 'testFlag';
    $regent->clearFlags();
    
    $this->assertSame('', $regent->flags);
});

test('flag', function ()
{
    $flag1st = 'i';
    $flag2nd = 'm';

    $regent = Regent::init();

    $startFlags = $regent->flags;
    $this->assertSame('', $startFlags);

    for($i = 0; $i <= 1; $i++)
    {
        $regent->flag(" $flag1st "); //? trim check
        $this->assertSame($flag1st, $regent->flags);
    }

    $regent->flag($startFlags);
    $this->assertSame($flag1st, $regent->flags);

    $regent->flag($flag2nd);
    $this->assertSame($flag1st . $flag2nd, $regent->flags);

    foreach([$flag1st, $startFlags, 'invalidFlag', 'validButNotUsedFlag' => 's'] as $flagForRemove)
    {
        $regent->flag($flagForRemove, false);
        $this->assertSame($flag2nd, $regent->flags);
    }
});

test('massFlags', function ()
{
    $flags1stSample = ['i', 'm', 's'];
    $flags2ndSample = ['U', 'S', 'D'];

    $regent = Regent::massFlags($flags1stSample);
    $this->assertSame(implode('', $flags1stSample), $regent->flags);

    $regent->massFlags($flags2ndSample);
    $this->assertSame(implode('', array_merge($flags1stSample, $flags2ndSample)), $regent->flags);

    $regent->massFlags($flags1stSample, false);
    $this->assertSame(implode('', $flags2ndSample), $regent->flags);
});

test('massFlagsAssoc', function ()
{
    $flags1stSample = ['i' => true, 'm' => false, 's' => true];
    $flags2ndSample = [];

    $result1st = $result2nd = '';

    foreach($flags1stSample as $flag => $add)
    {
        if($add)
            $result1st .= $flag;
        else
            $result2nd .= $flag;
        
        $flags2ndSample[$flag] = !$add;
    }

    $regent = Regent::massFlagsAssoc($flags1stSample);
    $this->assertSame($result1st, $regent->flags);

    $regent->massFlagsAssoc($flags2ndSample);
    $this->assertSame($result2nd, $regent->flags);

});

test('inlineFlags', function ()
{
    $flags1stSample = 'ims';
    $flags2ndSample = 'USD';

    $regent = Regent::inlineFlags($flags1stSample);
    $this->assertSame($flags1stSample, $regent->flags);

    $regent->inlineFlags($flags2ndSample);
    $this->assertSame($flags1stSample . $flags2ndSample, $regent->flags);

    $regent->inlineFlags($flags1stSample, false);
    $this->assertSame($flags2ndSample, $regent->flags);
});

test('all methods for specific flags with duplicated/remove func', function ()
{
    $methods = [
        'caseless' => 'i',
        'multiline' => 'm',
        'dotAll' => 's',
        'extended' => 'x',
        'anchored' => 'A',
        'dollarEndOnly' => 'D',
        'extraAnalysisOfPattern' => 'S',
        'ungreedy' => 'U',
        'extra' => 'X',
        'infoJChanged' => 'J',
        'utf8' => 'u',
    ];

    $regent = Regent::init();
    foreach($methods as $method => $result)
    {
        $regent->$method();
        $this->assertSame($result, $regent->flags);
        $regent->$method(false);
    }
});