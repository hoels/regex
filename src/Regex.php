<?php

namespace Regex;

abstract class Regex
{
    /**
     * @param string $regex The regular expression.
     * @param string $input The input string on which we want to apply the regular expression.
     * @return bool Returns whether there is a match.
     */
    public static function containsMatchIn(string $regex, string $input): bool
    {
        return preg_match($regex, $input) === 1;
    }

    /**
     * @param string $input The input string on which we want to apply the regular expression.
     * @param string $regex The regular expression.
     * @return MatchResult|null Returns a MatchResult for the first match that was found or null if there is not match.
     */
    public static function find(string $regex, string $input): ?MatchResult
    {
        // get matches
        $matchCount = preg_match(
            $regex,
            $input,
            $matches,
            PREG_OFFSET_CAPTURE | PREG_UNMATCHED_AS_NULL
        );

        // no match found
        if ($matchCount !== 1 || $matches === null) {
            return null;
        }

        // wrap match
        return self::setOfMatchesToMatchResult($matches);
    }

    /**
     * @param string $regex The regular expression.
     * @param string $input The input string on which we want to apply the regular expression.
     * @return MatchResult[] Returns an array of MatchResults for all matches. Empty array, if there is no match.
     */
    public static function findAll(string $regex, string $input): array
    {
        $matchCount = preg_match_all(
            $regex,
            $input,
            $matches,
            PREG_SET_ORDER | PREG_OFFSET_CAPTURE | PREG_UNMATCHED_AS_NULL
        );

        if ($matchCount === false || $matchCount < 1 || $matches === null) {
            return [];
        }

        $matchResults = [];
        foreach ($matches as $setOfMatches) {
            $matchResults[] = self::setOfMatchesToMatchResult($setOfMatches);
        }

        return $matchResults;
    }

    /**
     * @param array<mixed, array<int, string|null|int>> $matches The array with matches as returned from preg_match.
     * @return MatchResult The match result containing all information from preg_match.
     */
    private static function setOfMatchesToMatchResult(array $matches): MatchResult
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
