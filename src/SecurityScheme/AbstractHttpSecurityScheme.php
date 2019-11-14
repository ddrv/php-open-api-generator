<?php

declare(strict_types=1);

namespace Ddrv\OpenApiGenerator\SecurityScheme;

use Ddrv\OpenApiGenerator\Exception\InvalidSchemeException;

abstract class AbstractHttpSecurityScheme extends AbstractSecurityScheme
{

    /**
     * @var string
     */
    private $scheme;

    /**
     * @var string|null
     */
    protected $bearerFormat;

    /**
     * @param string      $scheme
     * @param string|null $bearerFormat
     *
     * @throws InvalidSchemeException
     */
    public function __construct(string $scheme, ?string $bearerFormat = null)
    {
        parent::__construct(self::TYPE_HTTP);
        $scheme = trim($scheme);
        if (!$scheme) {
            throw new InvalidSchemeException();
        }
        $this->scheme = $scheme;
        if (!is_null($bearerFormat)) {
            $bearerFormat = trim($bearerFormat);
        }
        if (!$bearerFormat) {
            $bearerFormat = null;
        }
        $this->bearerFormat = $bearerFormat;
    }

    public function getScheme(): string
    {
        return $this->scheme;
    }

    public function getBearerFormat(): ?string
    {
        return $this->bearerFormat;
    }

    public function toArray(bool $autoRef = true): array
    {
        $result = parent::toArray();
        $result['scheme'] = $this->getScheme();
        if ($this->getBearerFormat()) {
            $result['bearerFormat'] = $this->getBearerFormat();
        }
        return $result;
    }
}
