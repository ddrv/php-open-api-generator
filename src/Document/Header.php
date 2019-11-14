<?php

declare(strict_types=1);

namespace Ddrv\OpenApiGenerator\Document;

use Ddrv\OpenApiGenerator\Schema\AbstractSchema;

/**
 * @method Header setDescription(?string $description)
 * @method Header setDeprecated(bool $deprecated = true)
 * @method Header setRequired(bool $required = true)
 * @method Header setAllowEmptyValue(bool $allowEmptyValue = true)
 * @method Header setSchema(?AbstractSchema $schema)
 */
final class Header extends AbstractHeaderOrParameter
{
    public function toArray(bool $autoRef = true): array
    {
        if ($this->getRef() && $autoRef) {
            return ['$ref' => $this->getRef()];
        }
        return parent::toArray();
    }
}
