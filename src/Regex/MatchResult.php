<?php

namespace Regex;

class MatchResult
{
    /**
     * @param array<int|string, MatchGroup> $groups
     */
    public function __construct(
        private readonly string $value,
        private readonly int $offset,
        private readonly array $groups
    ) {
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
     * @return array<int|string, MatchGroup> An array of all matched groups with their index or name as key.
     */
    public function getGroups(): array
    {
        return $this->groups;
    }

    /**
     * @param int|string $identifier The index or name of the group.
     * @return MatchGroup|null Returns the match group if it exists.
     */
    public function getGroup(int|string $identifier): ?MatchGroup
    {
        return $this->groups[$identifier] ?? null;
    }
}
