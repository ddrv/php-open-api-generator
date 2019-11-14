<?php

declare(strict_types=1);

namespace Ddrv\OpenApiGenerator\Schema;

final class AllOfSchema extends AbstractSchemaMultiple
{

    public function __construct(AbstractSchema ...$schemas)
    {
        parent::__construct(self::TYPE_ALL_OF, ...$schemas);
    }
}
