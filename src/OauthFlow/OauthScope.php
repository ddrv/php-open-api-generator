<?php

declare(strict_types=1);

namespace Ddrv\OpenApiGenerator\OauthFlow;

use Ddrv\OpenApiGenerator\Exception\InvalidDescriptionException;
use Ddrv\OpenApiGenerator\Exception\InvalidScopeException;
use InvalidArgumentException;

final class OauthScope
{

    /**
     * @var string
     */
    private $scope;

    /**
     * @var string
     */
    private $description;

    /**
     * @param string $scope
     * @param string $description
     *
     * @throws InvalidDescriptionException
     * @throws InvalidScopeException
     */
    public function __construct(string $scope, string $description)
    {
        $this
            ->setScope($scope)
            ->setDescription($description)
        ;
    }

    /**
     * @param string $scope
     *
     * @return OauthScope
     *
     * @throws InvalidScopeException
     */
    public function setScope(string $scope): self
    {
        $scope = trim($scope);
        if (!$scope) {
            throw new InvalidScopeException();
        }
        $this->scope = $scope;
        return $this;
    }

    public function getScope(): string
    {
        return $this->scope;
    }

    /**
     * @param string $description
     *
     * @return OauthScope
     *
     * @throws InvalidDescriptionException
     */
    public function setDescription(string $description): self
    {
        $description = trim($description);
        if (!$description) {
            throw new InvalidDescriptionException();
        }
        $this->description = $description;
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }
}
