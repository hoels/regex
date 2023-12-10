<?php

namespace Regex;

use Exception;
use OutOfBoundsException;

abstract class Regex
{
    /**
     * @param string $pattern The pattern to search for.
     * @param string $subject The input string on which we want to apply the regular expression.
     * @return bool Returns whether there is a match.
     * @throws Exception If the expression is malformed.
     */
    public static function containsMatchIn(string $pattern, string $subject): bool
    {
        $matchCount = @preg_match(pattern: $pattern, subject: $subject);
        if ($matchCount === false) {
            $error = error_get_last();
            throw new Exception(message: $error["message"] ?? "", code: $error["type"] ?? 0);
        }

        return $matchCount > 0;
    }

    /**
     * @param string $pattern The pattern to search for.
     * @param string $subject The input string on which we want to apply the regular expression.
     * @return MatchResult|null Returns a MatchResult for the first match that was found or null if there is no match.
     * @throws Exception If the expression is malformed.
     */
    public static function find(string $pattern, string $subject): ?MatchResult
    {
        $matchCount = @preg_match(
            pattern: $pattern,
            subject: $subject,
            matches: $matches,
            flags: PREG_OFFSET_CAPTURE | PREG_UNMATCHED_AS_NULL
        );
        if ($matchCount === false) {
            $error = error_get_last();
            throw new Exception(message: $error["message"] ?? "", code: $error["type"] ?? 0);
        }

        if ($matchCount === 0) {
            return null;
        }

        /** @var array<int|string, array<int, string|null|int>> $matches */
        return self::convertMatchesToMatchResult($matches);
    }

    /**
     * @param string $pattern The pattern to search for.
     * @param string $subject The input string on which we want to apply the regular expression.
     * @return MatchResult[] Returns an array of MatchResults for all matches. Empty array, if there is no match.
     * @throws Exception If the expression is malformed.
     */
    public static function findAll(string $pattern, string $subject): array
    {
        $matchCount = @preg_match_all(
            pattern: $pattern,
            subject: $subject,
            matches: $matches,
            flags: PREG_SET_ORDER | PREG_OFFSET_CAPTURE | PREG_UNMATCHED_AS_NULL
        );
        if ($matchCount === false) {
            $error = error_get_last();
            throw new Exception(message: $error["message"] ?? "", code: $error["type"] ?? 0);
        }

        if ($matchCount === 0) {
            return [];
        }

        /** @var array<int|string, array<int, string|null|int>>[] $matches */
        $matchResults = [];
        foreach ($matches as $setOfMatches) {
            $matchResults[] = self::convertMatchesToMatchResult($setOfMatches);
        }

        return $matchResults;
    }

    /**
     * @param string $pattern The pattern to search for.
     * @param string $subject The input string on which we want to apply the regular expression.
     * @param int $index The index at which we want the match to start (beginning equals 0).
     * @return MatchResult|null Returns a MatchResult for the first match that was found or null if there is no match.
     * @throws OutOfBoundsException If the index is smaller than 0 or greater than the length of the input.
     * @throws Exception If the expression is malformed.
     */
    public static function matchAt(string $pattern, string $subject, int $index): ?MatchResult
    {
        if ($index < 0 || $index > strlen($subject)) {
            throw new OutOfBoundsException();
        }

        $matches = self::findAll(pattern: $pattern, subject: $subject);
        foreach ($matches as $match) {
            if ($match->getOffset() === $index) {
                return $match;
            }
        }

        return null;
    }

    /**
     * @param string $pattern The pattern of which all occurrences should be replaced.
     * @param string $subject The input string on which we want to apply the regular expression.
     * @param string $replacement The expression with which matches should be replaced.
     * @param int|null $limit Optionally, the maximum number of occurrences that should be replaced.
     * @return string The new string, including the replacements.
     * @throws Exception If the expression is malformed.
     */
    public static function replace(string $pattern, string $subject, string $replacement, ?int $limit = null): string
    {
        $output = @preg_replace(pattern: $pattern, replacement: $replacement, subject: $subject, limit: $limit ?? -1);
        if (!is_string($output)) {
            $error = error_get_last();
            throw new Exception(message: $error["message"] ?? "", code: $error["type"] ?? 0);
        }

        return $output;
    }

    /**
     * @param string $pattern The pattern of which the first occurrence should be replaced.
     * @param string $subject The input string on which we want to apply the regular expression.
     * @param string $replacement The expression with which matches should be replaced.
     * @return string The new string, including the replacements.
     * @throws Exception If the expression is malformed.
     */
    public static function replaceFirst(string $pattern, string $subject, string $replacement): string
    {
        return self::replace(pattern: $pattern, subject: $subject, replacement: $replacement, limit: 1);
    }

    /**
     * @param array<int|string, array<int, string|null|int>> $matches The array with matches as returned from
     * preg_match with the flags PREG_OFFSET_CAPTURE and PREG_UNMATCHED_AS_NULL.
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
