<?php

namespace MultihandED\Regent\Exceptions;

use MultihandED\Regent\Builder;
use Exception;

class RegentException extends Exception
{
    /**
     * Exception thrown in case of invalid delimiter
     *
     * @param string $passedValue - Invalid delimiter
     * @return self
     */
    public static function invalidDelimiter(string $passedValue) : self
    {
        $list = static::getValidListAsString(Builder::VALID_DELIMITERS);
        return new self("The delimiter must be in the list: $list. Passed value is: '$passedValue'.");
    }

    /**
     * Exception thrown in case of invalid modifier
     *
     * @param string $passedValue - Invalid modifier
     * @return self
     */
    public static function invalidModifier(string $passedValue) : self
    {
        $list = static::getValidListAsString(Builder::VALID_INLINE_MODIFIERS);
        return new self("The modifier must be in the list: $list. Passed value is: '$passedValue'.");
    }

    /**
     * Exception thrown if no modifiers are passed
     *
     * @return self
     */
    public static function notPassedModifiers() : self
    {
        return new self("No modifiers passed.");
    }

    /**
     * Exception thrown in case of invalid flag
     *
     * @param string $passedValue - Invalid flag
     * @return self
     */
    public static function invalidFlag(string $passedValue) : self
    {
        $list = static::getValidListAsString(Builder::VALID_FLAGS);
        return new self("The flag must be in the list: $list. Passed value is: '$passedValue'.");
    }

    /**
     * Exception thrown if no flags are passed
     *
     * @return self
     */
    public static function notPassedFlags() : self
    {
        return new self("No flags passed.");
    }

    /**
     * Converts an array of valid values to string form
     *
     * @param array $list - List of valid values
     * @return string
     */
    protected static function getValidListAsString(array $list): string
    {
        return implode(', ', array_map(
            fn($val) => "'$val'", $list
        ));
    }
}
