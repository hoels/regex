<?php

namespace Regex;

use Exception;
use OutOfBoundsException;

abstract class Regex
{
    /**
     * @param string $regex The regular expression.
     * @param string $input The input string on which we want to apply the regular expression.
     * @return bool Returns whether there is a match.
     * @throws Exception If the expression is malformed.
     */
    public static function containsMatchIn(string $regex, string $input): bool
    {
        $matchCount = @preg_match($regex, $input);
        if ($matchCount === false) {
            $error = error_get_last();
            throw new Exception($error["message"] ?? "", $error["type"] ?? 0);
        }

        return $matchCount > 0;
    }

    /**
     * @param string $input The input string on which we want to apply the regular expression.
     * @param string $regex The regular expression.
     * @return MatchResult|null Returns a MatchResult for the first match that was found or null if there is no match.
     * @throws Exception If the expression is malformed.
     */
    public static function find(string $regex, string $input): ?MatchResult
    {
        // get matches
        $matchCount = @preg_match(
            $regex,
            $input,
            $matches,
            PREG_OFFSET_CAPTURE | PREG_UNMATCHED_AS_NULL
        );
        if ($matchCount === false) {
            $error = error_get_last();
            throw new Exception($error["message"] ?? "", $error["type"] ?? 0);
        }

        // no match found
        if ($matchCount < 1 || $matches === null) {
            return null;
        }

        // wrap match
        return self::convertMatchesToMatchResult($matches);
    }

    /**
     * @param string $regex The regular expression.
     * @param string $input The input string on which we want to apply the regular expression.
     * @return MatchResult[] Returns an array of MatchResults for all matches. Empty array, if there is no match.
     * @throws Exception If the expression is malformed.
     */
    public static function findAll(string $regex, string $input): array
    {
        $matchCount = @preg_match_all(
            $regex,
            $input,
            $matches,
            PREG_SET_ORDER | PREG_OFFSET_CAPTURE | PREG_UNMATCHED_AS_NULL
        );
        if ($matchCount === false) {
            $error = error_get_last();
            throw new Exception($error["message"] ?? "", $error["type"] ?? 0);
        }

        if ($matchCount < 1 || $matches === null) {
            return [];
        }

        $matchResults = [];
        foreach ($matches as $setOfMatches) {
            $matchResults[] = self::convertMatchesToMatchResult($setOfMatches);
        }

        return $matchResults;
    }

    /**
     * @param string $regex The regular expression.
     * @param string $input The input string on which we want to apply the regular expression.
     * @param int $index The index at which we want the match to start (beginning equals 0).
     * @return MatchResult|null Returns a MatchResult for the first match that was found or null if there is no match.
     * @throws OutOfBoundsException If the index is smaller than 0 or greater than the length of the input.
     * @throws Exception If the expression is malformed.
     */
    public static function matchAt(string $regex, string $input, int $index): ?MatchResult
    {
        if ($index < 0 || $index > strlen($input)) {
            throw new OutOfBoundsException();
        }

        $matches = self::findAll($regex, $input);
        foreach ($matches as $match) {
            if ($match->getOffset() === $index) {
                return $match;
            }
        }

        return null;
    }

    /**
     * @param string $regex The regular expression.
     * @param string $input The input string on which we want to apply the regular expression.
     * @param string $replacement The expression with which matches should be replaced.
     * @return string The new string, including the replacements.
     * @throws Exception If the expression is malformed.
     */
    public static function replace(string $regex, string $input, string $replacement): string
    {
        $output = @preg_replace($regex, $replacement, $input);
        if (!is_string($output)) {
            $error = error_get_last();
            throw new Exception($error["message"] ?? "", $error["type"] ?? 0);
        }

        return $output;
    }

    /**
     * @param string $regex The regular expression.
     * @param string $input The input string on which we want to apply the regular expression.
     * @param string $replacement The expression with which matches should be replaced.
     * @return string The new string, including the replacements.
     * @throws Exception If the expression is malformed.
     */
    public static function replaceFirst(string $regex, string $input, string $replacement): string
    {
        $output = @preg_replace($regex, $replacement, $input, 1);
        if (!is_string($output)) {
            throw new Exception(preg_last_error_msg(), preg_last_error());
        }

        return $output;
    }

    /**
     * @param array<mixed, array<int, string|null|int>> $matches The array with matches as returned from preg_match.
     * @return MatchResult The match result containing all information from preg_match.
     */
    private static function convertMatchesToMatchResult(array $matches): MatchResult
    {
        $groups = [];
        foreach ($matches as $key => $array) {
            /** @var string|null $value */
            $value = $array[0];
            /** @var int $offset */
            $offset = $array[1];
            $groups[$key] = new MatchGroup($value, $offset);
        }

        /** @var string $value */
        $value = $matches[0][0];
        /** @var int $offset */
        $offset = $matches[0][1];

        return new MatchResult($value, $offset, $groups);
    }
}
