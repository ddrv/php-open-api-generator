<?php

declare(strict_types=1);

namespace Ddrv\OpenApiGenerator\OauthFlow;

use Ddrv\OpenApiGenerator\Exception\InvalidRefreshUrlException;
use Ddrv\OpenApiGenerator\Exception\RemovingLastScopeException;
use Ddrv\OpenApiGenerator\Document\Unit;

abstract class AbstractOauthFlow extends Unit
{

    /**
     * @var string|null
     */
    protected $refreshUrl;

    /**
     * @var string[]
     */
    protected $scopes = [];

    /**
     * @param string     $refreshUrl
     * @param OauthScope ...$scopes
     *
     * @throws InvalidRefreshUrlException
     */
    public function __construct(string $refreshUrl, OauthScope ...$scopes)
    {
        $this->setRefreshUrl($refreshUrl);
        foreach ($scopes as $item) {
            $this->addScope($item);
        }
    }

    /**
     * @param string $refreshUrl
     *
     * @throws InvalidRefreshUrlException
     */
    public function setRefreshUrl(string $refreshUrl)
    {
        $refreshUrl = trim($refreshUrl);
        if (!$refreshUrl) {
            throw new InvalidRefreshUrlException();
        }
        $this->refreshUrl = $refreshUrl;
    }

    public function getRefreshUrl(): ?string
    {
        return $this->refreshUrl;
    }

    public function addScope(OauthScope $scope)
    {
        $this->scopes[$scope->getScope()] = $scope;
        return $this;
    }

    /**
     * @param OauthScope $scope
     *
     * @return AbstractOauthFlow
     *
     * @throws RemovingLastScopeException
     */
    public function removeScope(OauthScope $scope): self
    {
        $key = $scope->getScope();
        if (array_key_exists($key, $this->scopes)) {
            if (count($this->scopes) === 1) {
                throw new RemovingLastScopeException();
            }
            unset($this->scopes[$key]);
        }
        return $this;
    }

    /**
     * @return OauthScope[]
     */
    public function getScopes(): array
    {
        return array_values($this->scopes);
    }

    public function toArray(): array
    {
        $result = [
            'refreshUrl' => $this->getRefreshUrl(),
        ];
        foreach ($this->getScopes() as $scope) {
            $result['scopes'][$scope->getScope()] = $scope->getDescription();
        }
        return $result;
    }

    abstract public function getType(): string;
}
