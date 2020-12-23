<?php

namespace Febalist\Laravel\Support;

use Illuminate\Support\Collection;

/** @mixin Collection */
class MacroCollection
{
    public function append()
    {
        return function ($items) {
            foreach ($items as $item) {
                $this->push($item);
            }

            return $this;
        };
    }

    public function without()
    {
        return function ($values) {
            if ($values instanceof Collection) {
                $values = $values->all();
            } elseif (!is_array($values)) {
                $values = func_get_args();
            }

            $items = array_without($this->all(), $values);

            return collect($items);
        };
    }

    public function remove()
    {
        return function ($values) {
            if ($values instanceof Collection) {
                $values = $values->all();
            } elseif (!is_array($values)) {
                $values = func_get_args();
            }

            $collection = collect($this->all())->without($values);

            $this->replace($collection);

            return $this;
        };
    }

    public function replace()
    {
        return function ($items) {
            if ($items instanceof Collection) {
                $items = $items->all();
            } elseif (!is_array($items)) {
                $items = func_get_args();
            }

            $this->splice(0);
            $this->append($items);

            return $this;
        };
    }
}
