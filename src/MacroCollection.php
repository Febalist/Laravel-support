<?php

namespace Febalist\Laravel\Support;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

/** @mixin Collection */
class MacroCollection
{
    /**
     * Append items onto the end of the collection.
     *
     * @param iterable $items
     * @return $this
     */
    public function append($items)
    {
        foreach ($items as $item) {
            $this->push($item);
        }

        return $this;
    }

    /**
     * Get new collection without the given values.
     *
     * @param iterable $values
     * @return static
     */
    public function without($values)
    {
        if ($values instanceof Collection) {
            $values = $values->all();
        } elseif (!is_array($values)) {
            $values = func_get_args();
        }

        $items = Arr::without($this->all(), $values);

        return collect($items);
    }

    /**
     * Remove elements with given values from collection.
     *
     * @param iterable $values
     * @return $this
     */
    public function remove($values)
    {
        if ($values instanceof Collection) {
            $values = $values->all();
        } elseif (!is_array($values)) {
            $values = func_get_args();
        }

        $collection = collect($this->all())->without($values);

        $this->replace($collection);

        return $this;
    }

    /**
     * Remove all items from collection and append specified items.
     *
     * @param iterable $items
     * @return $this
     */
    public function replace($items)
    {
        if ($items instanceof Collection) {
            $items = $items->all();
        } elseif (!is_array($items)) {
            $items = func_get_args();
        }

        $this->splice(0);
        $this->append($items);

        return $this;
    }
}
