<?php

declare(strict_types=1);

namespace Vigihdev\Validators;

use Vigihdev\Exceptions\Validation\FileException;

final class FileValidator
{
    public function __construct(
        private readonly string $field,
        private readonly string $value
    ) {}

    public static function validate(string $field, string $value): static
    {
        return new self($field, $value);
    }

    public function mustHaveExtension(): self
    {
        $ext = pathinfo($this->value, PATHINFO_EXTENSION);
        if (empty($ext)) {
            throw FileException::notHaveExtension($this->field, $this->value);
        }
        return $this;
    }


    public function mustBeNotExist(): self
    {
        if (file_exists($this->value)) {
            throw FileException::exist($this->field, $this->value);
        }
        return $this;
    }

    /**
     * Validate that file exists
     * 
     * @throws FileException
     */
    public function mustExist(): self
    {
        if (!file_exists($this->value)) {
            throw FileException::notFound($this->field, $this->value);
        }

        return $this;
    }

    /**
     * Validate that path is a file (not a directory)
     * 
     * @throws FileException
     */
    public function mustBeFile(): self
    {

        if (!is_file($this->value)) {
            throw FileException::notFile($this->field, $this->value);
        }

        return $this;
    }

    /**
     * Validate that file is readable
     * 
     * @throws FileException
     */
    public function mustBeReadable(): self
    {
        $this->mustExist();

        if (!is_readable($this->value)) {
            throw FileException::notReadable($this->field, $this->value);
        }

        return $this;
    }

    /**
     * Validate that file is writable
     * 
     * @throws FileException
     */
    public function mustBeWritable(): self
    {
        $this->mustExist();

        if (!is_writable($this->value)) {
            throw FileException::notWritable($this->field, $this->value);
        }

        return $this;
    }

    /**
     * Validate that file extension matches expected
     * 
     * @throws FileException
     */
    public function mustBeExtension(...$extensions): self
    {
        $ext = strtolower(pathinfo($this->value, PATHINFO_EXTENSION));
        if (!in_array($ext, array_map('strtolower', $extensions))) {
            throw FileException::invalidExtension($this->field, $ext, implode(', ', $extensions));
        }

        return $this;
    }


    /**
     * Validate that file is not empty
     * 
     * @throws FileException
     */
    public function mustNotBeEmpty(): self
    {
        $this->mustExist();

        if (filesize($this->value) === 0) {
            throw FileException::notEmpty($this->field, $this->value);
        }

        return $this;
    }

    /**
     * Validate that file size does not exceed maxSize
     * 
     * @throws FileException
     */
    public function mustNotExceedSize(int $maxSize): self
    {
        $this->mustExist();

        $actualSize = filesize($this->value);
        if ($actualSize > $maxSize) {
            throw FileException::tooBig($maxSize, $this->field, $actualSize);
        }

        return $this;
    }
}
