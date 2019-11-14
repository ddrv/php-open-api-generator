<?php

declare(strict_types=1);

namespace Ddrv\OpenApiGenerator\Document;

use Ddrv\OpenApiGenerator\Exception\InvalidUrlException;

final class Server extends Unit
{

    /**
     * @var string|null
     */
    private $url;

    /**
     * @var string|null
     */
    private $description;

    /**
     * @var ServerVariable[]
     */
    private $variables = [];

    /**
     * @var string[]
     */
    private $requiredVariables = [];

    /**
     * @param string      $url
     * @param string|null $description
     *
     * @throws InvalidUrlException
     */
    public function __construct(string $url, ?string $description = null)
    {
        $this
            ->setUrl($url)
            ->setDescription($description)
        ;
    }

    /**
     * @param string $url
     *
     * @return $this
     *
     * @throws InvalidUrlException
     */
    public function setUrl(string $url): self
    {
        $url = trim($url);
        if (!$url) {
            throw new InvalidUrlException();
        }
        $this->url = $url;
        preg_match_all('/{(?<var>[^}]+)}/ui', $url, $matches);
        $this->requiredVariables = [];
        if (array_key_exists('var', $matches)) {
            foreach ($matches['var'] as $var) {
                $this->requiredVariables[$var] = $var;
                if (!array_key_exists($var, $this->variables)) {
                    $this->variables[$var] = new ServerVariable('demo');
                }
            }
        }
        return $this;
    }

    public function getUrl(): string
    {
        return $this->url;
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

    public function setVariable(string $name, ServerVariable $variable): self
    {
        if (!array_key_exists($name, $this->requiredVariables)) {
            return $this;
        }
        $this->variables[$name] = $variable;
        return $this;
    }

    /**
     * @return ServerVariable[]
     */
    public function getVariables(): array
    {
        $result = [];
        foreach ($this->requiredVariables as $variable) {
            $result[$variable] = $this->variables[$variable];
        }
        return $result;
    }

    public function toArray(): array
    {
        $result = [
            'url' => $this->getUrl(),
        ];
        if ($this->getDescription()) {
            $result['description'] = $this->getDescription();
        }
        foreach ($this->getVariables() as $name => $variable) {
            $result['variables'][$name] = $variable->toArray();
        }
        return $result;
    }
}
