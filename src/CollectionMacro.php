<?php

namespace Febalist\Laravel\Support;

use Illuminate\Support\Collection;

/** @mixin Collection */
class CollectionMacro
{
    public static function boot()
    {
        if (!Collection::hasMacro('append')) {
            Collection::macro('append', function ($items) {
                foreach ($items as $item) {
                    $this->push($item);
                }

                return $this;
            });
        }

        if (!Collection::hasMacro('without')) {
            Collection::macro('without', function ($values) {
                if ($values instanceof Collection) {
                    $values = $values->all();
                } elseif (!is_array($values)) {
                    $values = func_get_args();
                }

                $items = array_without($this->all(), $values);

                return collect($items);
            });
        }

        if (!Collection::hasMacro('remove')) {
            Collection::macro('remove', function ($values) {
                if ($values instanceof Collection) {
                    $values = $values->all();
                } elseif (!is_array($values)) {
                    $values = func_get_args();
                }

                $collection = collect($this->all())->without($values);

                $this->replace($collection);

                return $this;
            });
        }

        if (!Collection::hasMacro('replace')) {
            Collection::macro('replace', function ($items) {
                if ($items instanceof Collection) {
                    $items = $items->all();
                } elseif (!is_array($items)) {
                    $items = func_get_args();
                }

                $this->splice(0);
                $this->append($items);

                return $this;
            });
        }
    }
}
