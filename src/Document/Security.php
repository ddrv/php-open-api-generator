<?php

declare(strict_types=1);

namespace Ddrv\OpenApiGenerator\Document;

use Ddrv\OpenApiGenerator\Exception\InvalidNameException;

final class Security extends Unit
{

    /**
     * @var string
     */
    private $name;

    /**
     * @var string[]
     */
    private $scopes = [];

    /**
     * @param string $name
     *
     * @throws InvalidNameException
     */
    public function __construct(string $name)
    {
        $this->setName($name);
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
        $this->checkName($name);
        $this->name = $name;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function addScope(string $scope): self
    {
        $scope = trim($scope);
        if (!$scope) {
            return $this;
        }
        $this->scopes[$scope] = $scope;
        return $this;
    }

    public function removeScope(string $scope): self
    {
        $scope = trim($scope);
        if (!array_key_exists($scope, $this->scopes)) {
            return $this;
        }
        unset($this->scopes[$scope]);
        return $this;
    }

    /**
     * @return string[]
     */
    public function getScopes(): array
    {
        return array_values($this->scopes);
    }

    /**
     * @param string $name
     *
     * @return bool
     *
     * @throws InvalidNameException
     */
    private function checkName(string $name): bool
    {
        if (!preg_match('/^[a-zA-Z0-9.\-_]+$/ui', $name)) {
            throw new InvalidNameException();
        };
        return true;
    }

    public function toArray(): array
    {
        return [
            $this->getName() => $this->getScopes(),
        ];
    }
}
