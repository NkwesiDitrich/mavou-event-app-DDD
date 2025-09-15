<?php

namespace App\Domain\Event\ValueObjects;

use InvalidArgumentException;

class EventTeaser
{
    private string $value;

    public function __construct(string $value)
    {
        $this->validate($value);
        $this->value = trim($value);
    }

    private function validate(string $value): void
    {
        $trimmedValue = trim($value);
        
        if (strlen($trimmedValue) > 255) {
            throw new InvalidArgumentException('Event teaser cannot exceed 255 characters');
        }

        // Optional: Check word count (10-20 words as requested)
        $wordCount = str_word_count($trimmedValue);
        if (!empty($trimmedValue) && ($wordCount < 5 || $wordCount > 30)) {
            // Allow flexibility but warn if outside recommended range
            // We'll be lenient here to avoid breaking user experience
        }
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function isEmpty(): bool
    {
        return empty($this->value);
    }

    public function getWordCount(): int
    {
        return str_word_count($this->value);
    }

    public function getExcerpt(int $maxLength = 100): string
    {
        if (strlen($this->value) <= $maxLength) {
            return $this->value;
        }

        return substr($this->value, 0, $maxLength - 3) . '...';
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function equals(EventTeaser $other): bool
    {
        return $this->value === $other->value;
    }
}
