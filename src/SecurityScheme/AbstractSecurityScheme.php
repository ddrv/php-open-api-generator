<?php

declare(strict_types=1);

namespace Ddrv\OpenApiGenerator\SecurityScheme;

use Ddrv\OpenApiGenerator\Document\Unit;
use InvalidArgumentException;

abstract class AbstractSecurityScheme extends Unit
{

    protected const TYPE_API_KEY = 'apiKey';
    protected const TYPE_HTTP    = 'http';
    protected const TYPE_OAUTH2  = 'oauth2';
    protected const TYPE_OPEN_ID = 'openIdConnect';

    protected const TYPES = [
        self::TYPE_API_KEY,
        self::TYPE_HTTP,
        self::TYPE_OAUTH2,
        self::TYPE_OPEN_ID,
    ];

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string|null
     */
    protected $description;

    /**
     * @var string|null
     */
    protected $ref;

    public function __construct(string $type)
    {
        if (!in_array($type, self::TYPES)) {
            $types = self::TYPES;
            $last = array_pop($types);
            $message = sprintf('argument type may be %s or %s', implode(', ', $types), $last);
            throw new InvalidArgumentException($message, 1);
        }
        $this->type = $type;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setDescription(?string $description): self
    {
        if ($description) {
            $description = trim($description);
        }
        if (!$description) {
            $description = null;
        }
        $this->description = $description;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getRef(): ?string
    {
        return $this->ref;
    }

    public function toArray(bool $autoRef = true): array
    {
        $result = [
            'type' => $this->getType(),
        ];
        if ($this->getDescription()) {
            $result['description'] = $this->getDescription();
        }
        return $result;
    }
}
