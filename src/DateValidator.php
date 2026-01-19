<?php

declare(strict_types=1);

namespace Vigihdev\Validators;

use Vigihdev\Exceptions\Validation\DateException;

final class DateValidator
{
    public const DATE_FORMAT = 'Y-m-d';
    public const DATE_TIME_FORMAT = 'Y-m-d H:i:s';

    public function __construct(
        private readonly string $field,
        private readonly ?string $value = null,
        private readonly string $format = self::DATE_FORMAT,
    ) {}

    private function isValid(): bool
    {
        $date = \DateTime::createFromFormat($this->format, $this->value);
        return $date && $date->format($this->format) === $this->value;
    }

    public static function validate(string $field, ?string $value = null, string $format = self::DATE_FORMAT): self
    {
        return new self($field, $value, $format);
    }

    private function mustNotBeEmpty(): self
    {
        $value = $this->value ?? '';
        if (empty(trim((string) $value))) {
            throw DateException::emptyValue($this->field);
        }
        return $this;
    }

    public function mustBeValidDate(): self
    {
        $this->mustNotBeEmpty();

        if (!$this->isValid()) {
            throw DateException::invalidDate($this->field, $this->value, $this->format);
        }
        return $this;
    }
}
