<?php

declare(strict_types=1);

namespace Ddrv\OpenApiGenerator\Document;

use Ddrv\OpenApiGenerator\Exception\RemovingLastOperationException;

final class PathItem extends AbstractPathItem
{

    /**
     * @param string $method
     *
     * @return $this
     *
     * @throws RemovingLastOperationException
     */
    public function removeOperation(string $method): self
    {
        $method = mb_strtolower(trim($method));
        if (array_key_exists($method, $this->operations)) {
            if (count($this->operations) === 1) {
                throw new RemovingLastOperationException();
            }
            unset($this->operations[$method]);
        }
        return $this;
    }
}
