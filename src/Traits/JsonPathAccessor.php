<?php

namespace Softonic\GraphQL\Traits;

trait JsonPathAccessor
{
    public function get(string $path)
    {
        if ($path === '.') {
            return $this;
        }

        $attributes = explode('.', $path);
        unset($attributes[0]);

        $attribute = array_shift($attributes);

        /**
         * This condition is needed because a child could have the same name than a MutationTypeConfig attribute
         * (type, linksTo, children).
         */
        $value = $this->hasChild($attribute) ? $this->children[$attribute] : $this->{$attribute};

        if ($attributes !== []) {
            return $value->get('.' . implode('.', $attributes));
        }

        return $value;
    }
}
