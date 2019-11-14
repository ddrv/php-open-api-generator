<?php

declare(strict_types=1);

namespace Ddrv\OpenApiGenerator\Document;

final class ServerVariable extends Unit
{

    /**
     * @var string
     */
    private $default;

    /**
     * @var string|null
     */
    private $description;

    /**
     * @var string[]
     */
    private $enum = [];

    /**
     * @param string|null $default
     * @param string|null $description
     */
    public function __construct(?string $default, ?string $description = null)
    {
        $this
            ->setDefault($default)
            ->setDescription($description)
        ;
    }

    /**
     * @param string      $default
     *
     * @return $this
     */
    public function setDefault(?string $default): self
    {
        $this->default = $default;
        return $this;
    }

    public function getDefault(): ?string
    {
        return $this->default;
    }

    public function setDescription(?string $description): self
    {
        if ($description) {
            $description = trim($description);
        }
        if (!$description) {
            $description = null;
        }
        $this->description = $description;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function addVariant(?string $value): self
    {
        $add = true;
        foreach ($this->enum as $item) {
            if ($item === $value) {
                $add = false;
            }
        }
        if ($add) {
            $this->enum[] = $value;
        }
        return $this;
    }

    public function removeVariant(?string $value): self
    {
        foreach ($this->enum as $key => $item) {
            if ($item === $value) {
                unset($this->enum[$key]);
                return $this;
            }
        }
        return $this;
    }

    public function getEnum(): array
    {
        $result = $this->enum;
        if (empty($result)) {
            return $result;
        }
        $add = true;
        foreach ($result as $item) {
            if ($item === $this->default) {
                $add = false;
            }
        }
        if ($add) {
            $result[] = $this->default;
        }
        return $result;
    }

    public function toArray(): array
    {
        $result = [
            'default' => $this->getDefault(),
        ];
        if ($this->getDescription()) {
            $result['description'] = $this->getDescription();
        }
        foreach ($this->getEnum() as $value) {
            $result['enum'][] = $value;
        }
        return $result;
    }
}
