<?php

namespace Softonic\GraphQL\Traits;

trait JsonPathAccessor
{
    public function get(string $path)
    {
        if ($path == '.') {
            return $this;
        }

        $attributes = explode('.', $path);
        unset($attributes[0]);

        $attribute = array_shift($attributes);
        $value     = $this->{$attribute};

        if (!empty($attributes)) {
            $value = $value->get('.' . implode('.', $attributes));
        }

        return $value;
    }
}
