<?php

namespace MultihandED\Regent;

use MultihandED\Regent\Exceptions\RegentException;

class Builder
{
    /**
     * Default delimiter
     *
     * @var string
     */
    protected static $delimiterDefault = '~';

    /**
     * Lists of valid values for flags/modifiers/delimiter
     */
    public const VALID_DELIMITERS = ['/', '~', '@', ';', '%', '`', '#'];
    public const VALID_FLAGS = ['i', 'm', 's', 'x', 'A', 'D', 'S', 'U', 'X', 'J', 'u'];
    public const VALID_INLINE_MODIFIERS = ['i' ,'m', 's', 'x', 'U', 'J'];

    /**
     * Current delimiter
     *
     * @var string|null
     */
    protected $delimiter = null;

    /**
     * Pattern of regex
     *
     * @var string
     */
    public string $pattern = '';

    /**
     * Flags of regex
     *
     * @var string
     */
    public string $flags = '';

    /**
     * Constructor of class
     * 
     * @param string|null $delimiter (Optional) - Delimiter value for the newly created object
     */
    protected function __construct(string $delimiter = null)
    {
        if(is_null($delimiter))
            $this->delimiter = static::$delimiterDefault;
        else
        {
            static::validateDelimiter($delimiter);
            $this->delimiter = $delimiter;
        }    
    }

    //-----------------------------------------------------
    // Common
    //-----------------------------------------------------

    /**
     * Create new instance of class
     * 
     * @param string|null $delimiter (Optional) - Delimiter value for the newly created object
     * @return self
     */
    public static function init(string $delimiter = null): self
    {
        return new self($delimiter);
    }

    /**
     * Resetting the pattern, flags and delimiter (optional) properties
     *
     * @param bool $withDelimiter (Optional) - Reset delimiter to default value
     * @return self
     */
    public function clear(bool $withDelimiter = false): self
    {
        if($withDelimiter)
            $this->delimiter = null;
        return $this->clearPattern()->clearFlags();
    }

    /**
     * Returning the final regular expression
     *
     * @return string
     */
    public function __toString(): string
    {
        return "{$this->getDelimiter()}{$this->pattern}{$this->getDelimiter()}{$this->flags}";
    }

    /**
     * Checking if a value is in a list
     *
     * @param string &$string - Value to validate
     * @param array $list - List of valid values
     * @param bool $letEmpty (Optional) - Allow empty strings
     * @return bool
     */
    protected static function validateInList(string &$string, array $list, bool $letEmpty = false): bool
    {
        $string = trim($string);

        return (($letEmpty && empty($string)) ||
                in_array($string, $list));
    }

    //-----------------------------------------------------
    // Delimiter
    //-----------------------------------------------------

    /**
     * Getting the default delimiter
     *
     * @return string
     */
    public static function getDelimiterDefault(): string
    {
        return static::$delimiterDefault;
    }

    /**
     * Setting the default delimiter
     *
     * @param string $newDelimiter - New default delimiter value
     * @return void
     */
    public static function setDelimiterDefault(string $newDelimiter): void
    {
        static::validateDelimiter($newDelimiter);
        static::$delimiterDefault = $newDelimiter;
    }

    /**
     * Delimiter validation
     *
     * @param string &$newDelimiter - Delimiter value to validate
     * @return void
     * @throws RegentException
     */
    protected static function validateDelimiter(string &$newDelimiter): void
    {
        if(!static::validateInList($newDelimiter, static::VALID_DELIMITERS))
            throw RegentException::invalidDelimiter($newDelimiter);
    }

    /**
     * Getting the delimiter
     *
     * @return string
     */
    public function getDelimiter(): string
    {
        if(empty($this->delimiter))
            $this->delimiter = static::$delimiterDefault;

        return $this->delimiter;
    }

    //-----------------------------------------------------
    // Inline modifiers
    //-----------------------------------------------------

    /**
     * Setting inline modifiers
     *
     * @param array $activeModifiers (Optional) - List of modifiers to be activated
     * @param array $deactiveModifiers (Optional) - List of modifiers to be deactivated
     * @return self
     * @throws RegentException
     */
    public function insertModifiers(array $activeModifiers = [], array $deactiveModifiers = []): self
    {
        if(empty($activeModifiers) && empty($deactiveModifiers))
            throw RegentException::notPassedModifiers();

        $modifiersGroups = [
            'active' => $activeModifiers,
            'deactive' => $deactiveModifiers
        ];

        $result = ['(?'];

        $deactiveMarked = false;
        foreach($modifiersGroups as $group => $modifiers)
        {
            foreach($modifiers as $modifier)
            {
                static::validateModifier($modifier);

                if(in_array($modifier, $result))
                    continue;
                
                if($group == 'deactive' && !$deactiveMarked)
                {
                    $result[] = '-';
                    $deactiveMarked = true;
                }

                $result[] = $modifier;
            }
        }

        $result[] = ')';

        return $this->concat($this->pattern, ...$result);
    }

    /**
     * Modifier validation
     *
     * @param string &$newModifier - Modifier value to validate
     * @return void
     * @throws RegentException
     */
    protected static function validateModifier(string &$newModifier): void
    {
        if (!static::validateInList($newModifier, static::VALID_INLINE_MODIFIERS))
            throw RegentException::invalidModifier($newModifier);
    }

    //-----------------------------------------------------
    // Flags
    //-----------------------------------------------------

    /**
     * Adding/removing flag
     *
     * @param string $flag - Flag value to add/remove
     * @param bool $add (Optional) - Add/Remove mode
     * @return self
     */
    public function flag(string $flag, bool $add = true) : self
    {
        if($add)
        {
            static::validateFlag($flag);

            if(empty($this->flags) || !empty($flag) &&
                mb_strpos($this->flags, $flag) === false)
                $this->flags .= $flag;
        }
        else
            $this->flags = str_replace($flag, '', $this->flags);

        return $this;
    }

    /**
     * Adding/removing flags via an array
     *
     * @param array $flags - List of flags values to add/remove
     * @param bool $add (Optional) - Add/Remove mode
     * @return self
     * @throws RegentException
     */
    public function massFlags(array $flags, bool $add = true): self
    {
        if(empty($flags))
            throw RegentException::notPassedFlags();

        foreach($flags as $flag)
        {
            $this->flag($flag, $add);
        }

        return $this;
    }

    /**
     * Adding/removing flags via an associative array
     *
     * @param array $flags - An associative array where the key is the character value of the flag and the value is the add/remove mode.
     * @return self
     * @throws RegentException
     */
    public function massFlagsAssoc(array $flags): self
    {
        if(empty($flags))
            throw RegentException::notPassedFlags();
        
        foreach($flags as $flag => $add)
        {
            $this->flag($flag, boolval($add));
        }

        return $this;
    }

    /**
     * Adding/removing flags via string
     *
     * @param string $inlineFlags - String of flag values to add/remove
     * @param bool $add (Optional) - Add/Remove mode
     * @return self
     */
    public function inlineFlags(string $inlineFlags, bool $add = true): self
    {
        return $this->massFlags(mb_str_split($inlineFlags), $add);
    }

    /**
     * Flag validation
     *
     * @param string &$newFlag - Flag value to validate
     * @return void
     * @throws RegentException
     */
    protected static function validateFlag(string &$newFlag): void
    {
        if(!static::validateInList($newFlag, static::VALID_FLAGS, true))
            throw RegentException::invalidFlag($newFlag);
    }

    /**
     * Set caseless mode
     *
     * @param bool $add (Optional) - Add/Remove mode
     * @return self
     */
    public function caseless(bool $add = true) : self
    {
        return $this->flag('i', $add);
    }

    /**
     * Set multiline mode
     *
     * @param bool $add (Optional) - Add/Remove mode
     * @return self
     */
    public function multiline(bool $add = true) : self
    {
        return $this->flag('m', $add);
    }

    /**
     * Allowing the "dot" metacharacter to find newlines
     *
     * @param bool $add (Optional) - Add/Remove mode
     * @return self
     */
    public function dotAll(bool $add = true) : self
    {
        return $this->flag('s', $add);
    }

    /**
     * Whitespace data characters in the pattern are totally ignored except when escaped or inside a character class, and characters between an unescaped # outside a character class and the next newline character, inclusive, are also ignored
     *
     * @param bool $add (Optional) - Add/Remove mode
     * @return self
     */
    public function extended(bool $add = true) : self
    {
        return $this->flag('x', $add);
    }

    /**
     * Pattern is forced to be "anchored"
     *
     * @param bool $add (Optional) - Add/Remove mode
     * @return self
     */
    public function anchored(bool $add = true) : self
    {
        return $this->flag('A', $add);
    }

    /**
     * Dollar metacharacter in the pattern matches only at the end of the subject string
     *
     * @param bool $add (Optional) - Add/Remove mode
     * @return self
     */
    public function dollarEndOnly(bool $add = true) : self
    {
        return $this->flag('D', $add);
    }

    /**
     * Set extra analysis mode
     *
     * @param bool $add (Optional) - Add/Remove mode
     * @return self
     */
    public function extraAnalysisOfPattern(bool $add = true) : self
    {
        return $this->flag('S', $add);
    }

    /**
     * Inverts the "greediness" of the quantifiers
     *
     * @param bool $add (Optional) - Add/Remove mode
     * @return self
     */
    public function ungreedy(bool $add = true) : self
    {
        return $this->flag('U', $add);
    }

    /**
     * Any backslash in a pattern that is followed by a letter that has no special meaning causes an error
     *
     * @param bool $add (Optional) - Add/Remove mode
     * @return self
     */
    public function extra(bool $add = true) : self
    {
        return $this->flag('X', $add);
    }

    /**
     * Allow duplicate names for subpatterns
     *
     * @param bool $add (Optional) - Add/Remove mode
     * @return self
     */
    public function infoJChanged(bool $add = true) : self
    {
        return $this->flag('J', $add);
    }

    /**
     * Pattern and subject strings are treated as UTF-8
     *
     * @param bool $add (Optional) - Add/Remove mode
     * @return self
     */
    public function utf8(bool $add = true) : self
    {
        return $this->flag('u', $add);
    }

    /**
     * Resetting the flags property
     *
     * @return self
     */
    public function clearFlags(): self
    {
        $this->flags = '';
        return $this;
    }

    //-----------------------------------------------------
    // Pattern
    //-----------------------------------------------------

    /**
     * Escape all metacharacters in string if it neccessary
     *
     * @param string &$string - String to escape
     * @param bool $quote - Necessity of escape a string
     * @return void
     */
    protected function quote(string &$string, bool $quote) : void
    {
        if($quote)
            $string = preg_quote($string, $this->getDelimiter());
    }

    /**
     * Concatenates the given string
     *
     * @param array ...$strings - Strings to concatenate
     * @return self
     */
    protected function concat(...$strings) : self
    {
        $this->pattern = implode('', $strings);
        return $this;
    }

    /**
     * Adds a string to the pattern
     *
     * @param string $string - String to add to the pattern
     * @param bool $quote (Optional) - Necessity of escape a string
     * @return self
     */
    public function addPattern(string $string, bool $quote = false): self
    {
        $this->quote($string, $quote);
        return $this->concat($this->pattern, $string);
    }

    /**
     * Declares the start of data (or string in multiline mode)
     *
     * @param string $string - String to add to the pattern
     * @param bool $quote (Optional) - Necessity of escape a string
     * @return self
     */
    public function startsWith(string $string = '', bool $quote = false): self
    {
        $this->quote($string, $quote);
        return $this->concat('^', $string, $this->pattern);
    }

    /**
     * Declares the end of the data, or before the end of the line (or the end of the line in multiline mode)
     *
     * @param string $string - String to add to the pattern
     * @param bool $quote (Optional) - Necessity of escape a string
     * @return self
     */
    public function endsWith(string $string = '', bool $quote = false): self
    {
        $this->quote($string, $quote);
        return $this->concat($this->pattern, $string, '$');
    }

    /**
     * Start of conditional selection branch
     *
     * @param string $string - String to add to the pattern
     * @param bool $quote (Optional) - Necessity of escape a string
     * @return self
     */
    public function or(string $string = '', bool $quote = false): self
    {
        $this->quote($string, $quote);
        return $this->concat($this->pattern, '|', $string);
    }

    /**
     * Beginning of subpattern
     *
     * @return self
     */
    public function openGroup(): self
    {
        return $this->concat($this->pattern, '(');
    }

    /**
     * End of subpattern
     *
     * @return self
     */
    public function closeGroup(): self
    {
        return $this->concat($this->pattern, ')');
    }

    /**
     * All/Except alphanumeric characters
     *
     * @param bool $except (Optional) - Switching a metacharacter to its opposite
     * @return self
     */
    public function alphaNum(bool $except = false): self
    {
        return $this->concat($this->pattern, $except ? '\W' : '\w');
    }

    /**
     * All/Except whitespace characters
     *
     * @param bool $except (Optional) - Switching a metacharacter to its opposite
     * @return self
     */
    public function whiteSpace(bool $except = false): self
    {
        return $this->concat($this->pattern, $except ? '\S' : '\s');
    }

    /**
     * All/Except vertical whitespace characters
     *
     * @param bool $except (Optional) - Switching a metacharacter to its opposite
     * @return self
     */
    public function whiteSpaceVertical(bool $except = false): self
    {
        return $this->concat($this->pattern, $except ? '\V' : '\v');
    }

    /**
     * All/Except horizontal whitespace characters
     *
     * @param bool $except (Optional) - Switching a metacharacter to its opposite
     * @return self
     */
    public function whiteSpaceHorizontal(bool $except = false): self
    {
        return $this->concat($this->pattern, $except ? '\H' : '\h');
    }

    /**
     * All/Except digits
     *
     * @param bool $except (Optional) - Switching a metacharacter to its opposite
     * @return self
     */
    public function digit(bool $except = false): self
    {
        return $this->concat($this->pattern, $except ? '\D' : '\d');
    }

    /**
     * (Except) Word boundary
     *
     * @param bool $except (Optional) - Switching a metacharacter to its opposite
     * @return self
     */
    public function borderOfWord(bool $except = false): self
    {
        return $this->concat($this->pattern, $except ? '\B' : '\b');
    }

    /**
     * Start of line
     *
     * @return self
     */
    public function startOfLine(): self
    {
        return $this->concat($this->pattern, '\A');
    }

    /**
     * End of line
     *
     * @return self
     */
    public function endOfLine(): self
    {
        return $this->concat($this->pattern, '\Z');
    }

    /**
     * End of action
     *
     * @return self
     */
    public function endOfAction(): self
    {
        return $this->concat($this->pattern, '\G');
    }

    /**
     * Start of character class declaration
     *
     * @param bool $except (Optional) - Switching a metacharacter to its opposite
     * @return self
     */
    public function openAnyOf(bool $except = false): self
    {
        $sign = '[';
        if($except)
            $sign .= '^';

        return $this->concat($this->pattern, $sign);
    }

    /**
     * End of character class declaration
     *
     * @return self
     */
    public function closeAnyOf(): self
    {
        return $this->concat($this->pattern, ']');
    }

    /**
     * Back reference
     *
     * @param int $num - Number of group
     * @return self
     */
    public function backReference(int $num): self
    {
        return $this->concat($this->pattern, '\\' . $num);
    }

    /**
     * Matches an element only if there is (not) an element before/after it
     *
     * @param bool $ahead - Direction
     * @param bool $except (Optional) - Switching a metacharacter to its opposite
     * @param string $string (Optional) - String to add to the pattern
     * @param bool $quote (Optional) - Necessity of escape a string
     * @return self
     */
    public function look(bool $ahead, bool $except = false, string $string = '', bool $quote = false): self
    {
        $sign = '?';
        if($ahead)
            $sign .= '<';
        $sign .= $except ? '!' : '=';

        $this->quote($string, $quote);
        return $this->concat($this->pattern, $sign, $string);
    }

    /**
     * Matches any character except newline (by default)
     *
     * @return self
     */
    public function anyCharacter(): self
    {
        return $this->concat($this->pattern, '.');
    }

    /**
     * Declares a character range
     *
     * @param string $from - Range start value
     * @param string $to - Range end value 
     * @return self
     */
    public function range(string $from, string $to): self
    {
        return $this->concat($this->pattern, $from, '-', $to);
    }

    /**
     * Resetting the pattern property
     *
     * @return self
     */
    public function clearPattern(): self
    {
        $this->pattern = '';
        return $this;
    }

    //-----------------------------------------------------
    // Quantifiers
    //-----------------------------------------------------

    /**
     * Switch greedy/lazy mode of quantifier
     *
     * @param string $quantifier - Quantifier
     * @param bool $lazy - Lazy mode
     * @return string
     */
    protected function switchToLazy(string $quantifier, bool $lazy): string
    {
        if($lazy)
            $quantifier .= '?';

        return $quantifier;
    }

    /**
     * Quantifier meaning 0 or more occurrences
     *
     * @param bool $lazy - Lazy mode
     * @param string $string (Optional) - String to add to the pattern
     * @param bool $quote (Optional) - Necessity of escape a string
     * @return self
     */
    public function zeroOrMore(bool $lazy = false, string $string = '', bool $quote = false): self
    {
        $this->quote($string, $quote);
        return $this->concat($this->pattern, $string, 
            $this->switchToLazy('*', $lazy));
    }

    /**
     * Quantifier meaning 1 or more occurrences
     *
     * @param bool $lazy (Optional) - Lazy mode
     * @param string $string (Optional) - String to add to the pattern
     * @param bool $quote (Optional) - Necessity of escape a string
     * @return self
     */
    public function oneOrMore(bool $lazy = false, string $string = '', bool $quote = false): self
    {
        $this->quote($string, $quote);
        return $this->concat($this->pattern, $string, 
            $this->switchToLazy('+', $lazy));
    }

    /**
     * Quantifier meaning 0 or 1 occurrence
     *
     * @param bool $lazy (Optional) - Lazy mode
     * @param string $string (Optional) - String to add to the pattern
     * @param bool $quote (Optional) - Necessity of escape a string
     * @return self
     */
    public function zeroOrOne(bool $lazy = false, string $string = '', bool $quote = false): self
    {
        $this->quote($string, $quote);
        return $this->concat($this->pattern, $string, 
            $this->switchToLazy('?', $lazy));
    }

    /**
     * Exact number of occurrences
     *
     * @param int $num - Number of occurrences
     * @param bool $lazy (Optional) - Lazy mode
     * @return self
     */
    public function exactly(int $num, bool $lazy = false): self
    {
        $sign = '{' . $num . '}';
        return $this->concat($this->pattern, 
            $this->switchToLazy($sign, $lazy));
    }

    /**
     * Minimal number of occurrences
     *
     * @param int $num - Minimal number of occurrences
     * @param bool $lazy (Optional) - Lazy mode
     * @return self
     */
    public function atLeast(int $num, bool $lazy = false): self
    {
        $sign = '{' . $num . ',}';
        return $this->concat($this->pattern, 
            $this->switchToLazy($sign, $lazy));
    }

    /**
     * Maximal number of occurrences
     *
     * @param int $num - Maximal number of occurrences
     * @param bool $lazy (Optional) - Lazy mode
     * @return self
     */
    public function atMax(int $num, bool $lazy = false): self
    {
        $sign = '{,' . $num . '}';
        return $this->concat($this->pattern, 
            $this->switchToLazy($sign, $lazy));
    }

    /**
     * Number of occurrences in a given range
     *
     * @param int $min - Minimal number of occurrences
     * @param int $max - Maximal number of occurrences
     * @param bool $lazy (Optional) - Lazy mode
     * @return self
     */
    public function between(int $min, int $max, bool $lazy = false): self
    {
        $sign = '{' . $min . ',' . $max . '}';
        return $this->concat($this->pattern, 
            $this->switchToLazy($sign, $lazy));
    }
}
