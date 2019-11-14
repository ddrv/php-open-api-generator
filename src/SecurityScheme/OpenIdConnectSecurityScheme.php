<?php

declare(strict_types=1);

namespace Ddrv\OpenApiGenerator\SecurityScheme;

use Ddrv\OpenApiGenerator\Exception\InvalidOpenIdConnectUrlException;

final class OpenIdConnectSecurityScheme extends AbstractSecurityScheme
{

    /**
     * @var string
     */
    private $openIdConnectUrl;

    /**
     * @param string $openIdConnectUrl
     *
     * @throws InvalidOpenIdConnectUrlException
     */
    public function __construct(string $openIdConnectUrl)
    {
        $this->setOpenIdConnectUrl($openIdConnectUrl);
        parent::__construct(self::TYPE_OPEN_ID);
    }

    /**
     * @param string $openIdConnectUrl
     *
     * @return $this
     *
     * @throws InvalidOpenIdConnectUrlException
     */
    public function setOpenIdConnectUrl(string $openIdConnectUrl): self
    {
        $openIdConnectUrl = trim($openIdConnectUrl);
        if (!$openIdConnectUrl) {
            throw new InvalidOpenIdConnectUrlException();
        }
        $this->openIdConnectUrl = $openIdConnectUrl;
        return $this;
    }

    public function getOpenIdConnectUrl(): string
    {
        return $this->openIdConnectUrl;
    }

    public function toArray(bool $autoRef = true): array
    {
        if ($this->getRef() && $autoRef) {
            return ['$ref' => $this->getRef()];
        }
        $result = parent::toArray();
        $result['openIdConnectUrl'] = $this->getOpenIdConnectUrl();
        return $result;
    }
}
