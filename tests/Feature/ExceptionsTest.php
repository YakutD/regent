<?php

use MultihandED\Regent\Facades\Regent;
use MultihandED\Regent\Exceptions\RegentException;

test('invalidFlag', function () 
{
    $invalidFlag = 'invalidFlag';
    $etalonException = RegentException::invalidFlag($invalidFlag);

    $this->assertInstanceOf(RegentException::class, $etalonException);

    $actualException = null;
    try 
    {
        Regent::flag($invalidFlag);
    } 
    catch (RegentException $e) 
    {
        $actualException = $e;
    }

    $this->assertInstanceOf($etalonException::class, $actualException);
    $this->assertSame($etalonException->getMessage(), $actualException->getMessage());
});

test('notPassedFlags', function () 
{
    $etalonException = RegentException::notPassedFlags();

    $this->assertInstanceOf(RegentException::class, $etalonException);

    foreach(['massFlags', 'massFlagsAssoc'] as $method)
    {
        $iterateException = null;
        try 
        {
            Regent::$method([]);
        } 
        catch (RegentException $e) 
        {
            $iterateException = $e;
        }

        $this->assertInstanceOf($etalonException::class, $iterateException);
        $this->assertSame($etalonException->getMessage(), $iterateException->getMessage());
    }
});

test('invalidModifier', function () 
{
    $invalidModifier = ''; //? empty string not allowed for inline modifiers
    $etalonException = RegentException::invalidModifier($invalidModifier);

    $this->assertInstanceOf(RegentException::class, $etalonException);

    $wrongArgs = array([$invalidModifier], []);
    for($i = 0; $i <= 1; $i++)
    {
        $iterateException = null;
        try 
        {
            Regent::insertModifiers($wrongArgs[0], $wrongArgs[1]);
        } 
        catch (RegentException $e) 
        {
            $iterateException = $e;
        }

        $this->assertInstanceOf($etalonException::class, $iterateException);
        $this->assertSame($etalonException->getMessage(), $iterateException->getMessage());

        $wrongArgs = array_reverse($wrongArgs);
    }
});

test('notPassedModifiers', function () 
{
    $etalonException = RegentException::notPassedModifiers();

    $this->assertInstanceOf(RegentException::class, $etalonException);

    $actualException = null;
    try 
    {
        Regent::insertModifiers();
    } 
    catch (RegentException $e) 
    {
        $actualException = $e;
    }

    $this->assertInstanceOf($etalonException::class, $actualException);
    $this->assertSame($etalonException->getMessage(), $actualException->getMessage());
});

test('invalidDelimiter', function () 
{
    $invalidDelimiter = ''; //? empty string not allowed for delimiter
    $etalonException = RegentException::invalidDelimiter($invalidDelimiter);

    $this->assertInstanceOf(RegentException::class, $etalonException);

    foreach(['setDelimiterDefault', 'init'] as $method)
    {
        $iterateException = null;
        try 
        {
            Regent::$method($invalidDelimiter);
        } 
        catch (RegentException $e) 
        {
            $iterateException = $e;
        }

        $this->assertInstanceOf($etalonException::class, $iterateException);
        $this->assertSame($etalonException->getMessage(), $iterateException->getMessage());
    }
});