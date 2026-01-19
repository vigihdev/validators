<?php

declare(strict_types=1);

namespace Vigihdev\Validators;

use Vigihdev\Exceptions\Validation\StringException;

final class StringValidator
{
    public function __construct(
        private readonly string $field,
        private readonly ?string $value = null,
    ) {}

    public static function validate(string $field, ?string $value = null): self
    {
        return new self($field, $value);
    }

    private function mustBeNotEmptyValue(): self
    {
        if ($this->value === null || $this->value === '') {
            throw StringException::emptyValue($this->field);
        }
        return $this;
    }

    /**
     * Validate that the string is not empty
     */
    public function notEmpty(): self
    {
        $string = $this->value;

        if ($string === null || $string === '') {
            throw StringException::emptyValue($this->field);
        }

        return $this;
    }

    /**
     * Validate that the string length is at least min characters
     */
    public function minLength(int $min): self
    {
        $this->mustBeNotEmptyValue();

        $actualLength = strlen($this->value);

        if ($actualLength < $min) {
            throw StringException::tooShort($min, $this->value, $this->field);
        }

        return $this;
    }

    /**
     * Validate that the string length is at most max characters
     */
    public function maxLength(int $max): self
    {
        $this->mustBeNotEmptyValue();
        $actualLength = strlen($this->value);

        if ($actualLength > $max) {
            throw StringException::tooLong($max, $this->value, $this->field);
        }

        return $this;
    }

    /**
     * Validate that the string length is between min and max characters
     */
    public function lengthBetween(int $min, int $max): self
    {
        $this->minLength($min);
        $this->maxLength($max);

        return $this;
    }

    /**
     * Validate that the string matches the expected value
     */
    public function equals(string $expected): self
    {
        $this->mustBeNotEmptyValue();

        if ($this->value !== $expected) {
            throw StringException::notEqual($expected, $this->value, $this->field);
        }

        return $this;
    }

    public function notMatches(string $pattern): self
    {
        $this->mustBeNotEmptyValue();

        if (preg_match_all($pattern, $this->value, $matches, PREG_SET_ORDER, 0)) {
            $match = implode(' ', array_map(fn($arr) => implode(' ', $arr), $matches));
            throw StringException::invalidCharacters($match, $this->value, $this->field);
        }

        return $this;
    }

    /**
     * Validate that the string matches a regex pattern
     */
    public function matches(string $pattern): self
    {
        $this->mustBeNotEmptyValue();

        if (!preg_match($pattern, $this->value)) {
            throw StringException::notMatch($pattern, $this->value, $this->field);
        }

        return $this;
    }

    /**
     * Validate that the string contains only alphanumeric characters
     */
    public function alphanumeric(): self
    {
        $this->mustBeNotEmptyValue();
        $pattern = '/^[a-zA-Z0-9]+$/';

        if (!preg_match($pattern, $this->value)) {
            throw StringException::invalidCharacters('Non-alphanumeric characters', $this->value, $this->field);
        }

        return $this;
    }

    /**
     * Validate that the string contains only alphabetic characters
     */
    public function alphabetic(): self
    {
        $this->mustBeNotEmptyValue();
        $pattern = '/^[a-zA-Z]+$/';

        if (!preg_match($pattern, $this->value)) {
            throw StringException::invalidCharacters('Non-alphabetic characters', $this->value, $this->field);
        }

        return $this;
    }
}
