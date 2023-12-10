<?php

namespace Regex;

class MatchGroup
{
    public function __construct(
        private readonly ?string $value,
        private readonly int $offset
    ) {
    }

    /**
     * @return string|null The value of the matched group if group was matched.
     */
    public function getValue(): ?string
    {
        return $this->value;
    }

    /**
     * @return int The offset of the matched group within the input string.
     */
    public function getOffset(): int
    {
        return $this->offset;
    }
}
