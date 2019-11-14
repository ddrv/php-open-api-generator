<?php

declare(strict_types=1);

namespace Ddrv\OpenApiGenerator\Schema;

final class AnyOfSchema extends AbstractSchemaMultiple
{

    public function __construct(AbstractSchema ...$schemas)
    {
        parent::__construct(self::TYPE_ANY_OF, ...$schemas);
    }
}
