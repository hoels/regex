# RegEx Wrapper
This is a wrapper around regular expressions.

Regular expressions in PHP are a common point of failure and working with match groups in particular can be a major
headache. Inspired by the functionality Kotlin offers with its `Regex` class, I decided to start implementing a similar
functionality for PHP. Hoping that one day, the PHP team will improve the `preg_*` functions, this library provides a
very simple wrapper around regular expressions in PHP.

In contrast to the Kotlin implementation, the delimiters (e.g. `/` at the beginning and end of the expression) have to
be set. This allows for greater flexibility.


## Installation

#### Requirements

- PHP 8.1+

#### Composer
```shell
composer require hoels/regex
```


## Documentation

### Regex class
The main class of this library is the `Regex` class. The class currently provides the following functions:

#### containsMatchIn

```php
Regex::containsMatchIn(string $regex, string $input): bool
```

Indicates whether there is at least one match in `$input` for the regex given in `$regex`.

#### find

```php
Regex::find(string $regex, string $input): MatchResult|null
```

Returns the first match in `$input` for the regex given in `$regex`. Returns `null` if there is no match and an
`MatchResult` object if there is a match.

#### findAll

```php
Regex::findAll(string $regex, string $input): MatchResult[]
```

Returns an array of `MatchResult` objects for all matches in `$input` for the regex given in `$regex`. Returns an
empty array if there is no match.

#### matchAt

```php
Regex::matchAt(string $regex, string $input, int $index): MatchResult|null
```

Returns the first match in `$input` for the regex given in `$regex`, only if the match starts at `$index`. Returns `null`
if there is no match and an `MatchResult` object if there is a match at `$index`.

#### replace

```php
Regex::replace(string $regex, string $input, string $replacement): string
```

Replaces all occurrences of `$regex` in `$input` by the replacement expression `$replacement`. Example:

```php
echo Regex::replace("/(\\d\\.\\d)\\.\\d+/", "We support PHP 8.1.26 and 8.2.13.", "$1"); // We support PHP 8.1 and 8.2.
```

#### replaceFirst

```php
Regex::replaceFirst(string $regex, string $input, string $replacement): string
```

Replaces the first occurrence of `$regex` in `$input` by the replacement expression `$replacement`. Example:

```php
echo Regex::replaceFirst("/(\\d\\.\\d)\\.\\d+/", "We support PHP 8.1.26 and 8.2.13.", "$1"); // We support PHP 8.1 and 8.2.3.
```


### MatchResult class
The `MatchResult` class has three properties:
- `value` (`string`): The matched string.
- `offset` (`int`): The offset of the matched string within the input string.
- `groups` (`MatchGroup[]`): An array of all matched groups.

#### Groups
The `groups` property contains an array of `MatchGroup` objects.

The first element (index 0) will always represent the matched string, therefore the `value` and `offset` properties of
the `MatchGroup` object will be the same as the ones of its `MatchResult` parent.

If the regular expression did not declare any groups, the first element will be the only one in the array.

If the regular expression does declare groups, there will be a `MatchGroup` object for every group in the order of
appearance. Therefore, the first group will have the index 1, the second the index 2, and so on. If there are named
groups, there will be additional array elements with the name as index. Note that the `MatchGroup` object will have the
same properties as the one with the numeric index, but the object reference will not be the same.


### MatchGroup class
The `MatchGroup` class has two properties:
- `value` (`string|null`): The matched string or `null` if the declared group is outside the matched string.
- `offset` (`int`): The offset of the matched string within the input string. -1 if the declared group is outside the
matched string.


## Usage

### `Regex::containsMatchIn`

```php
use Regex\Regex;

if (Regex::containsMatchIn(pattern: "/^[a-z]+$/", subject: $_GET["input"])) {
    ...
}
```

### `Regex::find`

```php
use Regex\Regex;

$matchResult = Regex::find(pattern: "/(?P<year>\d{4})\/(?P<month>\d{2})\/(?P<day>\d{2})/", subject: $input);
if ($matchResult !== null) {
    $year = $matchResult->getGroup("year")?->getValue();
    $month = $matchResult->getGroup("month")?->getValue();
    $day = $matchResult->getGroup("day")?->getValue();
}
```
