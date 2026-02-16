<?php

namespace Softonic\GraphQL\DataObjects\Mutation;

use Softonic\GraphQL\DataObjects\AbstractItem;
use Softonic\GraphQL\DataObjects\Mutation\Traits\MutationObjectHandler;

class Item extends AbstractItem implements MutationObject
{
    use MutationObjectHandler;

    /**
     * @var bool
     */
    private $hasChanged = false;

    public function __construct(array $arguments = [], protected array $config = [], bool $hasChanged = false)
    {
        parent::__construct($arguments);
        $this->hasChanged = $hasChanged;
    }

    public function __get(string $key): mixed
    {
        if ((!array_key_exists($key, $this->arguments) || ($this->arguments[$key] === null))
            && array_key_exists($key, $this->config)
            && (($this->config[$key]->type === Item::class) || ($this->config[$key]->type === Collection::class))
        ) {
            $mutationTypeClass = $this->config[$key]->type;

            $this->arguments[$key] = new $mutationTypeClass([], $this->config[$key]->children);
        }

        return parent::__get($key);
    }

    public function __set(string $key, mixed $value): void
    {
        if (!array_key_exists($key, $this->arguments) || $this->arguments[$key] !== $value) {
            $this->hasChanged = true;
        }

        parent::__set($key, $value);
    }

    public function __unset(string $key): void
    {
        unset($this->arguments[$key]);

        $this->hasChanged = true;
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

        return parent::jsonSerialize();
    }
}
