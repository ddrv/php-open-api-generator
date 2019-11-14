<?php

declare(strict_types=1);

namespace Ddrv\OpenApiGenerator\OauthFlow;

use Ddrv\OpenApiGenerator\Exception\InvalidRefreshUrlException;
use Ddrv\OpenApiGenerator\Exception\InvalidTokenUrlException;

abstract class AbstractOauthTokenUrlFlow extends AbstractOauthFlow
{

    /**
     * @var string
     */
    protected $tokenUrl;

    /**
     * @param string     $tokenUrl
     * @param string     $refreshUrl
     * @param OauthScope $scope
     * @param OauthScope ...$scopes
     *
     * @throws InvalidRefreshUrlException
     * @throws InvalidTokenUrlException
     */
    public function __construct(
        string $tokenUrl,
        string $refreshUrl,
        OauthScope $scope,
        OauthScope ...$scopes
    ) {
        parent::__construct($refreshUrl, $scope, ...$scopes);
        $this->setTokenUrl($tokenUrl);
    }

    /**
     * @param string $tokenUrl
     *
     * @return AbstractOauthTokenUrlFlow
     *
     * @throws InvalidTokenUrlException
     */
    public function setTokenUrl(string $tokenUrl): self
    {
        $tokenUrl = trim($tokenUrl);
        if (!$tokenUrl) {
            throw new InvalidTokenUrlException();
        }
        $this->tokenUrl = $tokenUrl;
        return $this;
    }

    public function getTokenUrl(): string
    {
        return $this->tokenUrl;
    }

    public function toArray(): array
    {
        $result = [
            'tokenUrl' => $this->getTokenUrl(),
        ];
        $result = array_merge($result, parent::toArray());
        return $result;
    }
}
