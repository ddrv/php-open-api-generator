<?php

declare(strict_types=1);

namespace Ddrv\OpenApiGenerator\Document;

use Ddrv\OpenApiGenerator\Exception\ArgumentOutOfListException;
use Ddrv\OpenApiGenerator\Exception\InvalidHeaderNameException;
use Ddrv\OpenApiGenerator\Exception\InvalidNameException;
use Ddrv\OpenApiGenerator\Schema\AbstractSchema;

/**
 * @method Parameter setDescription(?string $description)
 * @method Parameter setDeprecated(bool $deprecated = true)
 * @method Parameter setAllowEmptyValue(bool $allowEmptyValue = true)
 * @method Parameter setSchema(?AbstractSchema $schema)
 */
final class Parameter extends AbstractHeaderOrParameter
{

    public const IN_PATH   = 'path';
    public const IN_QUERY  = 'query';
    public const IN_HEADER = 'header';
    public const IN_COOKIE = 'cookie';

    private const IN = [
        self::IN_PATH,
        self::IN_QUERY,
        self::IN_HEADER,
        self::IN_COOKIE,
    ];

    private const IGNORED_HEADERS = [
        'accept',
        'content-type',
        'authorization',
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
     * @param string $in
     * @param string $name
     *
     * @throws ArgumentOutOfListException
     * @throws InvalidHeaderNameException
     * @throws InvalidNameException
     */
    public function __construct(string $in, string $name)
    {
        $this->required = self::DEFAULT_REQUIRED;
        $this
            ->setIn($in)
            ->setName($name)
        ;
        parent::__construct();
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
        $in = mb_strtolower(trim($in));
        if (!in_array($in, self::IN)) {
            throw new ArgumentOutOfListException('in', self::IN);
        }
        $this->in = $in;
        $this->setRequired($this->isRequired());
        return $this;
    }

    public function getIn(): string
    {
        return $this->in;
    }

    /**
     * @param string $name
     *
     * @return $this
     *
     * @throws InvalidHeaderNameException
     * @throws InvalidNameException
     */
    public function setName(string $name): self
    {
        $name = trim($name);
        if (!$name) {
            throw new InvalidNameException();
        }
        $ignored = self::IGNORED_HEADERS;
        if ($this->getIn() === self::IN_HEADER && in_array(mb_strtolower($name), $ignored)) {
            throw new InvalidHeaderNameException($ignored);
        }
        $this->name = $name;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setRequired(bool $required = true)
    {
        if ($this->getIn() === self::IN_PATH) {
            $required  = true;
        }
        $this->required = $required;
        return $this;
    }

    public function toArray(bool $autoRef = true): array
    {
        if ($this->getRef() && $autoRef) {
            return ['$ref' => $this->getRef()];
        }
        $result = [
            'in'              => $this->getIn(),
            'name'            => $this->getName(),
        ];
        $result = array_merge($result, parent::toArray());
        return $result;
    }
}
