<?php

namespace Regex;

class MatchResult
{
    private string $value;
    private int $offset;
    /** @var MatchGroup[] */
    private array $groups;


    /**
     * @param string $value
     * @param int $offset
     * @param MatchGroup[] $groups
     */
    public function __construct(
        string $value,
        int $offset,
        array $groups
    ) {
        $this->value = $value;
        $this->offset = $offset;
        $this->groups = $groups;
    }

    /**
     * @return string The matched string.
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @return int The offset of the matched string within the input string.
     */
    public function getOffset(): int
    {
        return $this->offset;
    }

    /**
     * @return MatchGroup[] An array of all matched groups.
     */
    public function getGroups(): array
    {
        return $this->groups;
    }

    /**
     * @param int|string $identifier The index or name of the group.
     * @return MatchGroup|null Returns the match group if it exists.
     */
    public function getGroup($identifier): ?MatchGroup
    {
        return array_key_exists($identifier, $this->groups) ? $this->groups[$identifier] : null;
    }
}
