<?php

declare(strict_types=1);

namespace Ddrv\OpenApiGenerator\SecurityScheme;

use Ddrv\OpenApiGenerator\Exception\InvalidNameException;
use Ddrv\OpenApiGenerator\Exception\ArgumentOutOfListException;

final class ApiKeySecurityScheme extends AbstractSecurityScheme
{

    public const IN_QUERY  = 'query';
    public const IN_HEADER = 'header';
    public const IN_COOKIE = 'cookie';

    private const IN = [
        self::IN_QUERY,
        self::IN_HEADER,
        self::IN_COOKIE,
    ];

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $in;

    /**
     * ApiKeySecurityScheme constructor.
     *
     * @param string $in
     * @param string $name
     *
     * @throws InvalidNameException
     * @throws ArgumentOutOfListException
     */
    public function __construct(string $in, string $name)
    {
        parent::__construct(self::TYPE_API_KEY);
        $this
            ->setName($name)
            ->setIn($in)
        ;
    }

    /**
     * @param string $name
     *
     * @return $this
     *
     * @throws InvalidNameException
     */
    public function setName(string $name): self
    {
        $name = trim($name);
        if (!$name) {
            throw new InvalidNameException();
        }
        $this->name = $name;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $in
     *
     * @return $this
     *
     * @throws ArgumentOutOfListException
     */
    public function setIn(string $in): self
    {
        $in = mb_strtolower($in);
        $all = self::IN;
        if (!in_array($in, $all)) {
            throw new ArgumentOutOfListException('in', $all);
        }
        $this->in = $in;
        return $this;
    }

    public function getIn(): string
    {
        return $this->in;
    }

    public function toArray(bool $autoRef = true): array
    {
        if ($this->getRef() && $autoRef) {
            return ['$ref' => $this->getRef()];
        }
        $result = parent::toArray();
        $result['name'] = $this->getName();
        $result['in'] = $this->getIn();
        return $result;
    }
}
