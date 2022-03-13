# RegEx Wrapper
This is a wrapper around regular expressions for PHP 7.4 to PHP 8.1.

Regular expressions in PHP are a common point of failure and working with match groups in particular can be a major
headache. Inspired by the functionality Kotlin offers with its `Regex` class, I decided to start implementing a similar
functionality for PHP. Hoping that one day the PHP team will improve the `preg_*` functions, this library provides a
very simple wrapper for regular expressions in PHP.

In contrast to the Kotlin implementation, the delimiters (`/` at the beginning and end) have to be set. This allows for
greater flexibility.


## Install
You can install this library using Composer:

```shell
composer require hoels/regex
```

## Examples
You can find a documentation of all functions, return value and properties below. Most times, examples are easier to
understand than documentation, so lets start with that.

The simplest use-case may be the following: Imagine you just want to perform an action, if a user given input only
consists of lowercase letters. You can do that as follows:

```php
if (Regex::containsMatchIn("/^[a-z]+$/", $_GET["input"])) {
    ...
}
```

The previous example is also easy in PHP, lets get to more advanced stuff. Imagine you want to parse a date of the
format yyyy/mm/dd out of a given string by using groups. You can do that as follows:

```php
$matchResult = Regex::find("/(?P<year>\d{4})\/(?P<month>\d{2})\/(?P<day>\d{2})/", $input);
if ($matchResult !== null) {
    $year = $matchResult->getGroup("year")?->getValue();
    $month = $matchResult->getGroup("month")?->getValue();
    $day = $matchResult->getGroup("day")?->getValue();
}
```

You can also use the `findAll` function, which will return an array of match results, if there are multiple matches.


## Features
The main class of this library is the `Regex` class. The class currently provides the following functions:

### containsMatchIn

```php
Regex::containsMatchIn(string $regex, string $input): bool
```

Indicates whether there is at least one match in `$input` for the regex given in `$regex`.

### find

```php
Regex::find(string $regex, string $input): MatchResult|null
```

Returns the first match in `$input` for the regex given in `$regex`. Returns `null` if there is no match and an
`MatchResult` object if there is a match.

### findAll

```php
Regex::findAll(string $regex, string $input): MatchResult[]
```

Returns an array of `MatchResult` objects for all matches in `$input` for the regex given in `$regex`. Returns an
empty array if there is no match.

### matchAt

```php
Regex::matchAt(string $regex, string $input, int $index): MatchResult|null
```

Returns the first match in `$input` for the regex given in `$regex`, only if the match starts at `$index`. Returns `null`
if there is no match and an `MatchResult` object if there is a match at `$index`.

### replace

```php
Regex::replace(string $regex, string $input, string $replacement): string
```

Replaces all occurrences of `$regex` in `$input` by the replacement expression `$replacement`. Example:

```php
echo Regex::replace("/(\\d\\.\\d)\\.\\d+/", "We support PHP 8.0.16 and 8.1.3.", "$1"); // We support PHP 8.0 and 8.1.
```

### replaceFirst

```php
Regex::replaceFirst(string $regex, string $input, string $replacement): string
```

Replaces the first occurrence of `$regex` in `$input` by the replacement expression `$replacement`. Example:

```php
echo Regex::replaceFirst("/(\\d\\.\\d)\\.\\d+/", "We support PHP 8.0.16 and 8.1.3.", "$1"); // We support PHP 8.0 and 8.1.3.
```


## MatchResult
The match result class has three properties:
- value (string): The matched string.
- offset (int): The offset of the matched string within the input string.
- groups (MatchGroup[]): An array of all matched groups.

### Groups
The `groups` property contains an array of `MatchGroup` objects.

The first element (index 0) will always be the matched string, therefore the `value` and `offset` properties of the
`MatchGroup` object will be the same as the ones of its `MatchResult` parent.

If the regex did not declare any groups, the first element will be the only one in the array.

If the regex does declare groups, there will be a `MatchGroup` object for every group in the order of appearance.
Therefore, the first group will have the index 1, the second the index 2, and so on. If there are named groups, there
will be additional array elements with the name as index. Note that the `MatchGroup` object will have the same
properties as the one with the numeric index, but the object will not be the same.

If a declared group is outside the matched string, the `value` property will be `null` and the `offset` will be -1.
