<?php

namespace Amol\Router\Routing;

class Parameters
{
    /**
     * @var string[]
     */
    private array $keys = [];

    /**
     * @var mixed[]
     */
    private array $values = [];

    /**
     * @param string[] $keys
     */
    public function addKeys(array $keys): void
    {
        $this->keys = array_merge($this->keys, $keys);
    }

    /**
     * @param mixed[] $values
     */
    public function addValues(array $values): void
    {
        $this->values = array_merge($this->values, $values);
    }

    /**
     * return the count of keys
     */
    public function count(): int
    {
        return count($this->keys);
    }

    /**
     * @return array<string, mixed>
     */
    public function all(): array
    {
        $data = [];
        foreach ($this->keys as $index => $key) {
            $data[$key] = $this->values[$index];
        }

        return $data;
    }

}
