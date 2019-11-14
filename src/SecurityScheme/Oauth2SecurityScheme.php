<?php

declare(strict_types=1);

namespace Ddrv\OpenApiGenerator\SecurityScheme;

use Ddrv\OpenApiGenerator\OauthFlow\AbstractOauthFlow;
use Ddrv\OpenApiGenerator\OauthFlow\OauthAuthorizationCodeFlow;
use Ddrv\OpenApiGenerator\OauthFlow\OauthClientCredentialsFlow;
use Ddrv\OpenApiGenerator\OauthFlow\OauthImplicitFlow;
use Ddrv\OpenApiGenerator\OauthFlow\OauthPasswordFlow;

final class Oauth2SecurityScheme extends AbstractSecurityScheme
{

    /**
     * @var AbstractOauthFlow[]
     */
    private $flows = [];

    public function __construct(AbstractOauthFlow ...$flows)
    {
        parent::__construct(self::TYPE_OAUTH2);
        foreach ($flows as $flow) {
            $this->setFlow($flow);
        }
    }

    public function setFlow(AbstractOauthFlow $flow): self
    {
        $this->flows[$flow->getType()] = $flow;
        return $this;
    }

    public function removeImplicitFlow(): self
    {
        if (array_key_exists('implicit', $this->flows)) {
            unset($this->flows['implicit']);
        }
        return $this;
    }

    public function getImplicitFlow(): ?OauthImplicitFlow
    {
        if (array_key_exists('implicit', $this->flows)) {
            return $this->flows['implicit'];
        }
        return null;
    }

    public function removePasswordFlow(): self
    {
        if (array_key_exists('password', $this->flows)) {
            unset($this->flows['password']);
        }
        return $this;
    }

    public function getPasswordFlow(): ?OauthPasswordFlow
    {
        if (array_key_exists('password', $this->flows)) {
            return $this->flows['password'];
        }
        return null;
    }

    public function removeClientCredentialsFlow(): self
    {
        if (array_key_exists('clientCredentials', $this->flows)) {
            unset($this->flows['clientCredentials']);
        }
        return $this;
    }

    public function getClientCredentialsFlow(): ?OauthClientCredentialsFlow
    {
        if (array_key_exists('clientCredentials', $this->flows)) {
            return $this->flows['clientCredentials'];
        }
        return null;
    }

    public function removeAuthorizationCodeFlow(): self
    {
        if (array_key_exists('authorizationCode', $this->flows)) {
            unset($this->flows['authorizationCode']);
        }
        return $this;
    }

    public function getAuthorizationCodeFlow(): ?OauthAuthorizationCodeFlow
    {
        if (array_key_exists('authorizationCode', $this->flows)) {
            return $this->flows['authorizationCode'];
        }
        return null;
    }

    /**
     * @return AbstractOauthFlow[]
     */
    public function getFlows(): array
    {
        return array_values($this->flows);
    }

    public function toArray(bool $autoRef = true): array
    {
        if ($this->getRef() && $autoRef) {
            return ['$ref' => $this->getRef()];
        }
        $result = parent::toArray();
        $result['flows'] = null;
        foreach ($this->getFlows() as $flow) {
            $result['flows'][$flow->getType()] = $flow->toArray();
        }
        return $result;
    }
}
