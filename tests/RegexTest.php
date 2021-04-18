<?php

namespace Tests\Regex;

use PHPUnit\Framework\TestCase;
use Regex\MatchResult;
use Regex\Regex;

class RegexTest extends TestCase
{
    /**
     * Data provider for the 'testContains' test.
     *
     * @return array<int, array<int, mixed>>
     */
    public function containsProvider(): array
    {
        return [
            [
                "/[a-z]+/",
                "abcde",
                true
            ],
            [
                "/^[a-z]+$/",
                "abcde",
                true
            ],
            [
                "/^[a-z]+$/",
                "abcde123",
                false
            ],
        ];
    }

    /**
     * Tests the Regex::contains function.
     *
     * @dataProvider containsProvider
     * @param string $regex
     * @param string $input
     * @param bool $expectedResult
     */
    public function testContains(string $regex, string $input, bool $expectedResult)
    {
        self::assertEquals($expectedResult, Regex::containsMatchIn($regex, $input));
    }

    /**
     * Data provider for the 'testFindNonNull' test. Note: You should only provide test datasets with input and
     * expression pairs, for which there will be matches, as the tests expects a MatchResult as output.
     *
     * @return array<int, array<int, mixed>>
     */
    public function findProvider(): array
    {
        return [
            // First dataset: Has no groups.
            [
                "/[a-z]+/", // regex
                "abcde", // input
                "abcde", // expected match value
                0, // expected offset of match
                [ // groups
                    0 => ["abcde", 0]
                ]
            ],

            // Second dataset: Has one unnamed group.
            [
                "/,(.*)$/", // regex
                "Hello, my name is Jeff", // input
                ", my name is Jeff", // expected match value
                5, // expected offset of match
                [ // groups
                    0 => [", my name is Jeff", 5],
                    1 => [" my name is Jeff", 6]
                ]
            ],

            // Third dataset: Has one named group.
            [
                "/,(?P<someGroup>.*)$/", // regex
                "Hello, my name is Jeff", // input
                ", my name is Jeff", // expected match value
                5, // expected offset of match
                [ // groups
                    0 => [", my name is Jeff", 5],
                    1 => [" my name is Jeff", 6],
                    "someGroup" => [" my name is Jeff", 6],
                ]
            ],

            // Fourth dataset: Has 3 groups, while one of them is named.
            [
                "/^.*?([0-9]+).*\.\s(?P<sentenceGroup>.*?([0-9]+).*\.)$/", // regex
                "I am 22 years old. My brother is 18.", // input
                "I am 22 years old. My brother is 18.", // expected match value
                0, // expected offset of match
                [ // groups
                    0 => ["I am 22 years old. My brother is 18.", 0],
                    1 => ["22", 5],
                    2 => ["My brother is 18.", 19],
                    3 => ["18", 33],
                    "sentenceGroup" => ["My brother is 18.", 19]
                ]
            ],

            // Fifth dataset: Group that is not matched.
            [
                "/^(?P<numeric>\d+)|(?P<alphabetic>\w+)$/", // regex
                "Word", // input
                "Word", // expected match value
                0, // expected offset of match
                [ // groups
                    0 => ["Word", 0],
                    1 => [null, -1],
                    2 => ["Word", 0],
                    "numeric" => [null, -1],
                    "alphabetic" => ["Word", 0]
                ]
            ]
        ];
    }

    /**
     * Helper function to compare the given result with the excepted result.
     *
     * @param MatchResult $matchResult
     * @param string $expectedValue
     * @param string $expectedOffset
     * @param array<mixed, array<int, mixed>> $expectedGroups
     */
    private function checkMatchResult(
        MatchResult $matchResult,
        string $expectedValue,
        string $expectedOffset,
        array $expectedGroups
    ) {
        // Check if value and offset are correct.
        self::assertEquals($expectedValue, $matchResult->getValue());
        self::assertEquals($expectedOffset, $matchResult->getOffset());

        // Check if group count is correct and for each expected match group, if it exists and if value and offset
        // equal.
        $groups = $matchResult->getGroups();
        self::assertCount(count($expectedGroups), $groups);
        foreach ($expectedGroups as $key => $match) {
            self::assertArrayHasKey($key, $groups);
            self::assertEquals($match[0], $groups[$key]->getValue());
            self::assertEquals($match[1], $groups[$key]->getOffset());
        }
    }

    /**
     * Tests the Regex::find function for inputs that should output a MatchResult.
     *
     * @dataProvider findProvider
     * @param string $regex
     * @param string $input
     * @param string $expectedValue
     * @param int $expectedOffset
     * @param array<mixed, array<int, mixed>> $expectedGroups
     */
    public function testFindNonNull(
        string $regex,
        string $input,
        string $expectedValue,
        int $expectedOffset,
        array $expectedGroups
    ) {
        $matchResult = Regex::find($regex, $input);
        self::assertNotNull($matchResult);
        $this->checkMatchResult($matchResult, $expectedValue, $expectedOffset, $expectedGroups);
    }

    /**
     * Tests the Regex::find function for inputs that should output null.
     */
    public function testFindNull()
    {
        $regex = "/^[a-z]+$/";
        $input = "abcde123";
        self::assertNull(Regex::find($regex, $input));
    }

    /**
     * Data provider for the 'testFindAll' test.
     *
     * @return array<int, array<int, mixed>>
     */
    public function findAllProvider(): array
    {
        return [
            // First dataset: Matches a word of arbitrary length. There should be 4 matches. None of the matches has a
            // group.
            [
                "/\w+/", // regex
                "My name is Jeff", // input
                [ // matches with groups
                    ["My", 0, [0 => ["My", 0]]],
                    ["name", 3, [0 => ["name", 3]]],
                    ["is", 8, [0 => ["is", 8]]],
                    ["Jeff", 11, [0 => ["Jeff", 11]]],
                ]
            ],

            // Second dataset: Matches a data of the format y/m/d, where the year can consist of 2 or 4 digits and the
            // month and day can consist of 1 or 2 digits. There should be 2 matches. Each match should have 3 groups
            // for each part of the date.
            [
                "/(?P<year>\d{4}|\d{2})\/(?P<month>\d{1,2})\/(?P<day>\d{1,2})/", // regex
                "2019/4/20, 20/06/9", // input
                [ // matches with groups
                    ["2019/4/20", 0, [
                        0 => ["2019/4/20", 0],
                        1 => ["2019", 0],
                        2 => ["4", 5],
                        3 => ["20", 7],
                        "year" => ["2019", 0],
                        "month" => ["4", 5],
                        "day" => ["20", 7]
                    ]],
                    ["20/06/9", 11, [
                        0 => ["20/06/9", 11],
                        1 => ["20", 11],
                        2 => ["06", 14],
                        3 => ["9", 17],
                        "year" => ["20", 11],
                        "month" => ["06", 14],
                        "day" => ["9", 17]
                    ]]
                ]
            ],
        ];
    }

    /**
     * Tests the Regex::findAll function.
     *
     * @dataProvider findAllProvider
     * @param string $regex
     * @param string $input
     * @param array<int, array<int, mixed>> $expectedMatches
     */
    public function testFindAll(string $regex, string $input, array $expectedMatches)
    {
        // Check if the correct number of matches was found.
        $matchResults = Regex::findAll($regex, $input);
        self::assertCount(count($expectedMatches), $matchResults);

        // Iterate over all expected matches.
        foreach ($expectedMatches as $key => $match) {
            $this->checkMatchResult($matchResults[$key], $match[0], $match[1], $match[2]);
        }
    }
}
