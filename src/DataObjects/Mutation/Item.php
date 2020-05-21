<?php

namespace Softonic\GraphQL\DataObjects\Mutation;

use Softonic\GraphQL\Config\MutationTypeConfig;
use Softonic\GraphQL\DataObjects\AbstractItem;
use Softonic\GraphQL\DataObjects\Mutation\Traits\MutationObjectHandler;

class Item extends AbstractItem implements MutationObject, \JsonSerializable
{
    use MutationObjectHandler;

    /**
     * @var array
     */
    protected $arguments;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var bool
     */
    private $hasChanged = false;

    public function __construct(array $arguments = [], array $config = [], bool $hasChanged = false)
    {
        $this->arguments  = $arguments;
        $this->config     = $config;
        $this->hasChanged = $hasChanged;
    }

    public function __get(string $key)
    {
        if ((!array_key_exists($key, $this->arguments) || ($this->arguments[$key] === null))
            && array_key_exists($key, $this->config)
            && ($this->config[$key]->type !== MutationTypeConfig::SCALAR_DATA_TYPE)
        ) {
            $mutationTypeClass = $this->config[$key]->type;

            $this->arguments[$key] = new $mutationTypeClass([], $this->config[$key]->children);
        }

        return array_key_exists($key, $this->arguments) ? $this->arguments[$key] : null;
    }

    public function __set(string $key, $value): void
    {
        if (array_key_exists($key, $this->arguments)) {
            if ($this->arguments[$key] !== $value) {
                $this->setValue($key, $value);
            }
        } else {
            $this->setValue($key, $value);
        }
    }

    private function setValue(string $key, $value): void
    {
        $this->arguments[$key] = $value;

        $this->hasChanged = true;
    }

    public function __unset(string $key): void
    {
        unset($this->arguments[$key]);

        $this->hasChanged = true;
    }

    public function has(string $key): bool
    {
        $keyPath  = explode('.', $key);
        $firstKey = array_shift($keyPath);

        if (!array_key_exists($firstKey, $this->arguments)) {
            return false;
        }

        if (empty($keyPath)) {
            return true;
        }

        $nextKey = implode('.', $keyPath);

        return $this->arguments[$firstKey]->has($nextKey);
    }

    public function exists(array $data): bool
    {
        return $data === $this->arguments;
    }

    public function set(array $data): void
    {
        foreach ($data as $key => $value) {
            $this->{$key} = $value;
        }
    }

    public function jsonSerialize(): array
    {
        if (!$this->hasChanged()) {
            return [];
        }

        $item = [];
        foreach ($this->arguments as $key => $value) {
            if ($value instanceof FilteredCollection && !$value->hasChildren()) {
                continue;
            }

            if ($value instanceof \JsonSerializable) {
                if (!empty($valueSerialized = $value->jsonSerialize())) {
                    $item[$key] = $valueSerialized;
                }
            } else {
                $item[$key] = $value;
            }
        }

        return $item;
    }
}
