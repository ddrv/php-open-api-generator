<?php

declare(strict_types=1);

namespace Ddrv\OpenApiGenerator\Schema;

final class OneOfSchema extends AbstractSchemaMultiple
{

    public function __construct(AbstractSchema ...$schemas)
    {
        parent::__construct(self::TYPE_ONE_OF, ...$schemas);
    }
}
