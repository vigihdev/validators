<?php

declare(strict_types=1);

namespace Vigihdev\Validators;

use Vigihdev\Exceptions\Validation\DirectoryException;

final class DirectoryValidator
{

    private function __construct(
        private readonly string $field,
        private readonly ?string $value = null,
    ) {}

    public static function validate(string $field, ?string $value = null): self
    {
        return new self($field, $value);
    }

    private function mustBeNotEmptyValue(): self
    {
        $value = $this->value ?? '';
        if (empty(trim((string) $value))) {
            throw DirectoryException::emptyValue($this->field, $this->value);
        }
        return $this;
    }

    public function mustExist(): self
    {
        $this->mustBeNotEmptyValue();

        if (!is_dir($this->value)) {
            throw DirectoryException::notExist($this->field, $this->value);
        }
        return $this;
    }

    public function mustNotExist(): self
    {
        $this->mustBeNotEmptyValue();

        if (is_dir($this->value)) {
            throw DirectoryException::alreadyExists($this->field, $this->value);
        }
        return $this;
    }

    public function mustBeReadable(): self
    {
        $this->mustExist();

        if (!is_readable($this->value)) {
            throw DirectoryException::notReadable($this->field, $this->value);
        }
        return $this;
    }

    public function mustBeWritable(): self
    {
        $this->mustExist();

        if (!is_writable($this->value)) {
            throw DirectoryException::notWritable($this->field, $this->value);
        }
        return $this;
    }

    public function mustBeEmpty(): self
    {
        $this->mustExist();

        if (!($handle = opendir($this->value))) {
            throw DirectoryException::cannotScan($this->field, $this->value);
        }

        while (false !== ($entry = readdir($handle))) {
            if ($entry != "." && $entry != "..") {
                closedir($handle);
                throw DirectoryException::notEmpty($this->field, $this->value);
            }
        }

        closedir($handle);
        return $this;
    }


    public function ensureExists(): self
    {
        if (!is_dir($this->value)) {
            $parentDir = dirname($this->value);

            if (!is_dir($parentDir) && !mkdir($parentDir, 0755, true)) {
                throw DirectoryException::cannotCreate($this->field, $parentDir);
            }

            if (!mkdir($this->value, 0755, true)) {
                throw DirectoryException::cannotCreate($this->field, $this->value);
            }
        }

        return $this;
    }


    public function ensureDeletable(bool $recursive = false): self
    {
        $this->mustExist();

        if (!$recursive && $this->isNotEmpty()) {
            throw DirectoryException::notEmpty($this->field, $this->value);
        }

        return $this;
    }

    /**
     * Periksa apakah direktori tidak kosong.
     *
     * @return bool
     */
    public function isNotEmpty(): bool
    {
        $this->mustExist();

        if (!($handle = opendir($this->value))) {
            throw DirectoryException::cannotScan($this->field, $this->value);
        }

        $hasContent = false;
        while (false !== ($entry = readdir($handle))) {
            if ($entry != "." && $entry != "..") {
                $hasContent = true;
                break;
            }
        }

        closedir($handle);
        return $hasContent;
    }
}
