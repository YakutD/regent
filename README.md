# Regent

## About

The package for Laravel allows you to write regular expressions declaratively.

For example:

- contains only letters of the English alphabet or numbers
- length from 5 to 10 characters

Instead of writing the regular expression directly, you can do it in a more elegant way:

```php
echo Regent::startsWith()
                ->openAnyOf()
                    ->range('a', 'z')
                    ->digit()
                ->closeAnyOf()
                ->between(5, 10)
                ->endsWith()
                ->caseless(); // ~^[a-z\d]{5,10}$~i
```

In the case of more complex regular expressions, you can separately collect its subpatterns, which is very convenient, especially when it is possible to reuse and modify them in the process of building other regular expressions:
 
```php
    // regex to search for email in text
    $anyOf = Regent::openAnyOf()
                    ->range('a', 'z')
                    ->range(0, 9)
                    ->addPattern('_-')
                ->closeAnyOf()->pattern;

    $startsWith= Regent::openGroup()
                    ->addPattern($anyOf)
                    ->oneOrMore()
                    ->addPattern('.', true)
                  ->closeGroup()->pattern;

    $secondGroup = Regent::openGroup()
                        ->addPattern('.', true)
                        ->addPattern($anyOf)
                        ->oneOrMore()
                   ->closeGroup()->pattern;

    $firstSegment = Regent::zeroOrMore()
                    ->addPattern($anyOf)
                    ->oneOrMore()
                    ->addPattern('@')
                    ->addPattern($anyOf)
                    ->oneOrMore()->pattern;

    $endWith = Regent::zeroOrMore()
                    ->addPattern('.', true)
                    ->openAnyOf()
                        ->range('a', 'z')
                    ->closeAnyOf()
                    ->between(2, 6)->pattern;

    
    echo Regent::startsWith($startsWith)
                ->addPattern($firstSegment)
                ->addPattern($secondGroup)
                ->endsWith($endWith)
                ->caseless(); // ~^([a-z0-9_-]+\.)*[a-z0-9_-]+@[a-z0-9_-]+(\.[a-z0-9_-]+)*\.[a-z]{2,6}$~i
```

## How to install

You can install the package with the composer:

```
composer require multihanded/regent
```

## How to use

The package is supposed to be used through the facade:

```php
use MultihandED\Regent\Facades\Regent;
```

After constructing the expression, you can simply cast the resulting object to a string.

Note that the facade is configured to **always** return a new instance of the regular expression builder class.

## Documentation

### Public properties

- **pattern** (string) - regular expression pattern, empty string by default.
- **flags**  (string) - regular expression flags, empty string by default.

It is not recommended to set these properties directly, however you are free to do so at your own discretion.

### Public methods

- static **init** (string *$delimiter* = null): static - creates and returns an instance of the regular expression builder. Takes a delimiter as an argument. If no delimiter is provided, the default delimiter will be set.

- **clear**(bool *$withDelimiter* = false): self - sets the **pattern** and **flags** properties to empty strings. If the *$withDelimiter* argument is *true* then the delimiter will be set to its default value. Returns a reference to the call object.

#### Delimiter

- static **setDelimiterDefault**(string *$newDelimiter*) - allows you to set a default delimiter. Once set, all newly created Regent objects will use *$newDelimiter* as their delimiter. Returns nothing.

- static **getDelimiterDefault**(): string - returns the current value of the default delimiter.

- **getDelimiter**(): string - returns the delimiter value of the call object.

**Regent** accepts one of these characters as a valid delimiter: **/**, **~**, **@**, **;**, **%**, **`** **#**. By default, the default delimiter is **~** (tilde).

You can also set the default delimiter in *AppServiceProvider* of your project:

```php
public function boot()
{
    Regent::setDelimiterDefault('%');
}
```

#### Flags

List of valid flags: **i**, **m**, **s**, **x**, **A**, **D**, **S**, **U**, **X**, **J**, **u**. You can read more in the <a href="https://www.php.net/manual/en/reference.pcre.pattern.modifiers.php">official PHP documentation</a>.

- **flag**(string *$flag*, bool *$add* = true): self - adds the flag specified in the *$flag* argument to the **flags** property. Ignores empty lines and duplicate flags. If the *$add* argument is *false* then the flag will be removed (if this flag is not in **flags** it will be ignored). Returns a reference to the call object.

- **massFlags**(array *$flags*, bool *$add* = true): self - 
adds the values specified in the *$flags* argument to the **flags** property. Throws an exception when passing an empty array in *$flags*. Otherwise, it works similar to the **flag** method. Returns a reference to the call object.

- **massFlagsAssoc**(array *$flags*): self - 
adds the values specified in the *$flags* argument to the **flags** property. Throws an exception when passing an empty array in *$flags*. The *$flags* argument must be an associative array, where the key is the flag to add and the value is *$add* as in the **flag** and **massFlags** methods. For example, an array like ["i" => true, "m" => false] would add the **i** flag and remove the **m** flag. Returns a reference to the call object.

- **inlineFlags**(string *$inlineFlags*, bool $add = true): self - adds the flags specified in the *$inlineFlags* argument to the **flags** property. *$inlineFlags* is a string of consecutive flags, such as 'ims'. Otherwise, it works similar to the **flag** method. Returns a reference to the call object.

- **caseless**(bool *$add* = true) : self - adds the **i** (PCRE_CASELESS) flag to the **flags** property. Otherwise, it works similar to the **flag** method. Returns a reference to the call object.

- **multiline**(bool *$add* = true) : self - adds the **m** (PCRE_MULTILINE) flag to the **flags** property. Otherwise, it works similar to the **flag** method. Returns a reference to the call object.

- **dotAll**(bool *$add* = true) : self - adds the **s** (PCRE_DOTALL) flag to the **flags** property. Otherwise, it works similar to the **flag** method. Returns a reference to the call object.

- **extended**(bool *$add* = true) : self - adds the **x** (PCRE_EXTENDED) flag to the **flags** property. Otherwise, it works similar to the **flag** method. Returns a reference to the call object.

- **anchored**(bool *$add* = true) : self - adds the **A** (PCRE_ANCHORED) flag to the **flags** property. Otherwise, it works similar to the **flag** method. Returns a reference to the call object.

- **dollarEndOnly**(bool *$add* = true) : self - adds the **D** (PCRE_DOLLAR_ENDONLY) flag to the **flags** property. Otherwise, it works similar to the **flag** method. Returns a reference to the call object.

- **extraAnalysisOfPattern**(bool *$add* = true) : self - adds the **S** (extra analysis) flag to the **flags** property. Otherwise, it works similar to the **flag** method. Returns a reference to the call object.

- **ungreedy**(bool *$add* = true) : self - adds the **U** (PCRE_UNGREEDY) flag to the **flags** property. Otherwise, it works similar to the **flag** method. Returns a reference to the call object.

- **extra**(bool *$add* = true) : self - adds the **X** (PCRE_EXTRA) flag to the **flags** property. Otherwise, it works similar to the **flag** method. Returns a reference to the call object.

- **infoJChanged**(bool *$add* = true) : self - adds the **J** (PCRE_INFO_JCHANGED) flag to the **flags** property. Otherwise, it works similar to the **flag** method. Returns a reference to the call object.

- **utf8**(bool *$add* = true) : self - adds the **u** (PCRE_UTF8) flag to the **flags** property. Otherwise, it works similar to the **flag** method. Returns a reference to the call object.

- **clearFlags**(): self - sets the **flags** property to an empty string. Returns a reference to the call object.

#### Inline modifiers

List of allowed local (inline) modifiers: **i**, **m**, **s**, **x**, **U**, **J**.

- **insertModifiers**(array *$activeModifiers* = [], array *$deactiveModifiers* = []): self - setting inline modifiers by adding a string construction of the form **(?\*-\*)** to the **pattern** property, where **\*** before the hyphen sign are active modifiers (*$activeModifiers*), and \* after it are modifiers to be disabled (*$deactiveModifiers*) . Unlike the **flag** method, passing empty strings as an inline modifier is not allowed. Duplicate values are ignored (*$activeModifiers* takes precedence). If both *$activeModifiers* and *$deactiveModifiers* are empty arrays an exception will be thrown. Returns a reference to the call object.

#### Pattern

- ***addPattern***(string *$string*, bool *$quote* = false): self - adds to the **pattern** property the string passed in the *$string* argument. If the *$quote* argument is *true* then the *$string* function will be applied <a href="https://www.php.net/manual/en/function.preg-quote.php">preg_quote</a>. Returns a reference to the call object.

- **startsWith**(string *$string* = '', bool *$quote* = false): self - declares the start of data (or string in multiline mode) by adding the metacharacter **^** at the beginning of **pattern**. The string passed in the *string* argument is concatenated directly **after** the metacharacter. Otherwise, it works similarly to the **addPattern** method. Returns a reference to the call object.

- **endsWith**(string *$string* = '', bool *$quote* = false): self - declares the end of the data, or before the end of the line (or the end of the line in multiline mode) by appending the metacharacter **$** to the end of **pattern**. The string passed in the *string* argument is concatenated directly **before** the metacharacter. Otherwise, it works similarly to the **addPattern** method. Returns a reference to the call object.

- **or**(string *$string* = '', bool *$quote* = false): self - start of conditional selection branch by adding the **|** metacharacter to the **pattern** property. The string passed in the *string* argument is concatenated directly **after** the metacharacter. Otherwise, it works similarly to the **addPattern** method. Returns a reference to the call object.

- **anyCharacter**(): self - search of matches any character except newline (by default) by adding the metacharacter **.** (dot) to the **pattern** property. Returns a reference to the call object.

- **openGroup**(): self - declares the beginning of subpattern by adding the **(** metacharacter to the **pattern** property. Returns a reference to the call object.

- **closeGroup**(): self - declares the end of subpattern by adding the **)** metacharacter to the **pattern** property. Returns a reference to the call object.

- **alphaNum**(bool *$except* = false): self - search all/except alphanumeric characters by adding the **\w** metacharacter to the **pattern** property if the *$except* argument is *false* or **\W** if it is *true*. Returns a reference to the call object.

- **whiteSpace**(bool *$except* = false): self - search all/except whitespace characters by adding the **\s** metacharacter to the **pattern** property if the *$except* argument is *false* or **\S** if it is *true*. Returns a reference to the call object.

- **whiteSpaceVertical**(bool *$except* = false): self - search all/except vertical whitespace characters by adding the **\v** metacharacter to the **pattern** property if the *$except* argument is *false* or **\V** if it is *true*. Returns a reference to the call object.

- **whiteSpaceHorizontal**(bool *$except* = false): self - search all/except horizontal whitespace characters by adding the **\h** metacharacter to the **pattern** property if the *$except* argument is *false* or **\H** if it is *true*. Returns a reference to the call object.

- **digit**(bool *$except* = false): self - search all/except digits by adding the **\d** metacharacter to the **pattern** property if the *$except* argument is *false* or **\D** if it is *true*. Returns a reference to the call object.

- **borderOfWord**(bool *$except* = false): self - search all/except word boundary by adding the **\b** metacharacter to the **pattern** property if the *$except* argument is *false* or **\B** if it is *true*. Returns a reference to the call object.

- **startOfLine**(): self - declares the start of line by adding the **\A** metacharacter to the **pattern** property. Returns a reference to the call object.

- **endOfLine**(): self - declares the end of line by adding the **\Z** metacharacter to the **pattern** property. Returns a reference to the call object.

- **endOfAction**(): self - declares the end of action by adding the **\G** metacharacter to the **pattern** property. Returns a reference to the call object.

- **openAnyOf**(bool *$except* = false): self - declares the start of character class declaration by adding the **[** metacharacter to the **pattern** property if the *$except* argument is *false* or **[^** if it is *true*. Returns a reference to the call object.

- **closeAnyOf**(): self - declares the end of character class declaration by adding the **]** metacharacter to the **pattern** property. Returns a reference to the call object.

- **backReference**(int *$num*): self - declares the back reference by adding a string construction of the form **\\\\\*** to the **pattern** property, where **\*** is the number passed in the *$num* argument. Returns a reference to the call object.

- **look**(bool *$ahead*, bool *$except* = false, string *$string* = '', bool *$quote* = false): self - search of matches an element only if there is (not) an element before/after it by adding one of the following metacharacters to the **pattern** property depending on the *$ahead* and *$except* arguments:
    - **?=** - *$ahead* is *true*, *except* is *false*;
    - **?\!** - *$ahead* is *true*, *except* is *true*;
    - **?<=** - *$ahead* is *false*, *except* is *false*;
    - **?<\!** - *$ahead* is *true*, *except* is *true*.

 The string passed in the *string* argument is concatenated directly **after** the metacharacter. Otherwise, it works similarly to the **addPattern** method. Returns a reference to the call object.

- **range**(string *$from*, string *$to*): self - declares a character range by adding a string construction of the form **\*-\*** to the **pattern** property, where **\*** before the hyphen sign is the argument *$from* and after is argument *$to*. Returns a reference to the call object.

- **clearPattern**(): self - sets the **pattern** property to an empty string. Returns a reference to the call object.

#### Quantifiers

The *$lazy* argument specifies the greedy/lazy mode (by default, all quantifiers work in greedy mode).

- **zeroOrMore**(bool *$lazy* = false, string *$string* = '', bool *$quote* = false): self - adds the **\*** (0 or more occurrences) quantifier to the **pattern** property. The string passed in the *string* argument is concatenated directly **before** the quantifier. Otherwise, it works similarly to the **addPattern** method. Returns a reference to the call object.

- **oneOrMore**(bool *$lazy* = false, string *$string* = '', bool *$quote* = false): self - adds the **+** (1 or more occurrences) quantifier to the **pattern** property. The string passed in the *string* argument is concatenated directly **before** the quantifier. Otherwise, it works similarly to the **addPattern** method. Returns a reference to the call object.

- **zeroOrOne**(bool *$lazy* = false, string *$string* = '', bool *$quote* = false): self - adds the **?** (0 or 1 occurrence) quantifier to the **pattern** property. The string passed in the *string* argument is concatenated directly **before** the quantifier. Otherwise, it works similarly to the **addPattern** method. Returns a reference to the call object.

- **exactly**(int *$num*, bool *$lazy* = false): self - adds a string construction of the form **{\*}** (exact number of occurrences) to the **pattern** property, where **\*** is the number passed in the *$num* argument. Returns a reference to the call object.

- **atLeast**(int *$num*, bool *$lazy* = false): self - adds a string construction of the form **{\*,}** (minimal number of occurrences) to the **pattern** property, where **\*** is the number passed in the *$num* argument. Returns a reference to the call object.

- **atMax**(int *$num*, bool *$lazy* = false): self - adds a string construction of the form **{,\*}** (maximal number of occurrences) to the **pattern** property, where **\*** is the number passed in the *$num* argument. Returns a reference to the call object.

- **between**(int *$min*, int *$max*, bool *$lazy* = false): self - adds a string construction of the form **{\*,\*}** (occurrences in a given range) to the **pattern** property, where **\*** before the comma is the number passed in the *$min* argument and after in the *$max* argument. Returns a reference to the call object.
