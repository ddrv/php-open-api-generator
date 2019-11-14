<?php

declare(strict_types=1);

namespace Ddrv\OpenApiGenerator\Schema;

use Ddrv\OpenApiGenerator\Exception\ArgumentOutOfListException;
use Ddrv\OpenApiGenerator\Exception\InvalidPatternException;
use Ddrv\OpenApiGenerator\Exception\MaximalLimitShouldBeBiggerException;
use Ddrv\OpenApiGenerator\Exception\MinimalLimitShouldBeBiggerException;
use Throwable;

/**
 * @method StringSchema setFormat(?string $format)
 * @method StringSchema setNullable(bool $nullable = true)
 * @method StringSchema setDescription(?string $description)
 */
final class StringSchema extends AbstractSchemaSimple
{

    public const FORMAT_DATE      = 'date';
    public const FORMAT_DATE_TIME = 'date-time';
    public const FORMAT_PASSWORD  = 'password';
    public const FORMAT_BYTE      = 'byte';
    public const FORMAT_BINARY    = 'binary';
    public const FORMAT_EMAIL     = 'email';
    public const FORMAT_UUID      = 'uuid';
    public const FORMAT_URI       = 'uri';
    public const FORMAT_HOSTNAME  = 'hostname';
    public const FORMAT_IP_V4     = 'ipv4';
    public const FORMAT_IP_V6     = 'ipv6';

    /**
     * @var int|null
     */
    private $minLength;

    /**
     * @var int|null
     */
    private $maxLength;

    /**
     * @param string|null $format
     * @param string|null $pattern
     *
     * @throws ArgumentOutOfListException
     * @throws InvalidPatternException
     */
    public function __construct(?string $format = null, ?string $pattern = null)
    {
        parent::__construct(parent::TYPE_STRING, $format);
        $this->setPattern($pattern);
    }

    /**
     * @param int|null $minLength
     *
     * @return $this
     *
     * @throws MinimalLimitShouldBeBiggerException
     */
    public function setMinLength(?int $minLength): self
    {
        if (!is_null($minLength) && $minLength < 0) {
            throw new MinimalLimitShouldBeBiggerException('minLength', 0);
        }
        $this->minLength = $minLength;
        return $this;
    }

    public function getMinLength(): ?int
    {
        return $this->minLength;
    }

    /**
     * @param int|null $maxLength
     *
     * @return $this
     *
     * @throws MaximalLimitShouldBeBiggerException
     */
    public function setMaxLength(?int $maxLength): self
    {
        $minLength = $this->getMinLength();
        if (!is_null($maxLength)) {
            if (!is_null($minLength) && $maxLength < $minLength) {
                throw new MaximalLimitShouldBeBiggerException('maxLength', $minLength);
            }
            if ($maxLength <= 0) {
                throw new MaximalLimitShouldBeBiggerException('maxLength', 1);
            }
        }
        $this->maxLength = $maxLength;
        return $this;
    }

    public function getMaxLength(): ?int
    {
        return $this->maxLength;
    }

    /**
     * @param string|null $pattern
     *
     * @return $this
     *
     * @throws InvalidPatternException
     */
    public function setPattern(?string $pattern)
    {
        if (!is_null($pattern)) {
            $check = $pattern;
            if (!preg_match('/^\/(.*)\/([a-z]+)?$/ui', $pattern)) {
                $check = '/' . $pattern . '/';
            }
            try {
                preg_match($check, '');
            } catch (Throwable $exception) {
                throw new InvalidPatternException();
            }
        };
        $this->pattern = $pattern;
        return $this;
    }

    public function toArray(bool $autoRef = true): array
    {
        if ($this->getRef() && $autoRef) {
            return ['$ref' => $this->getRef()];
        }
        $result = parent::toArray();
        if (!is_null($this->getMinLength())) {
            $result['minLength'] = $this->getMinLength();
        }
        if (!is_null($this->getMaxLength())) {
            $result['maxLength'] = $this->getMaxLength();
        }
        return $result;
    }

    protected function getAllowedFormats(): array
    {
        return [
            self::FORMAT_DATE,
            self::FORMAT_DATE_TIME,
            self::FORMAT_PASSWORD,
            self::FORMAT_BYTE,
            self::FORMAT_BINARY,
            self::FORMAT_EMAIL,
            self::FORMAT_UUID,
            self::FORMAT_URI,
            self::FORMAT_HOSTNAME,
            self::FORMAT_IP_V4,
            self::FORMAT_IP_V6,
        ];
    }
}
