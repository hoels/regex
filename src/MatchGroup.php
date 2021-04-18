<?php

namespace Regex;

class MatchGroup
{
    private ?string $value;
    private int $offset;
    private bool $isNull;


    /**
     * MatchGroup constructor.
     * @param string|null $value
     * @param int $offset
     */
    public function __construct(
        ?string $value,
        int $offset
    ) {
        $this->value = $value;
        $this->offset = $offset;
        $this->isNull = $value === null;
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

    /**
     * @return bool Indicates, whether the group is outside of the matched string.
     */
    public function isNull(): bool
    {
        return $this->isNull;
    }
}
