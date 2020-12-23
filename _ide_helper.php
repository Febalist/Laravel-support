<?php

namespace Illuminate\Database\Schema {

    use Febalist\Laravel\Support\MacroBlueprint;

    /** @mixin MacroBlueprint */
    class Blueprint
    {

    }
}

namespace Illuminate\Database\Eloquent {

    use Illuminate\Support\HigherOrderCollectionProxy as Proxy;

    /**
     * @property-read Proxy average
     * @property-read Proxy avg
     * @property-read Proxy contains
     * @property-read Proxy each
     * @property-read Proxy every
     * @property-read Proxy filter
     * @property-read Proxy first
     * @property-read Proxy flatMap
     * @property-read Proxy groupBy
     * @property-read Proxy keyBy
     * @property-read Proxy map
     * @property-read Proxy max
     * @property-read Proxy min
     * @property-read Proxy partition
     * @property-read Proxy reject
     * @property-read Proxy sortBy
     * @property-read Proxy sortByDesc
     * @property-read Proxy sum
     * @property-read Proxy unique
     */
    class Collection extends \Illuminate\Support\Collection
    {

    }
}

namespace Illuminate\Support {

    use Illuminate\Database\Eloquent\Model;

    /** @mixin Model */
    class HigherOrderCollectionProxy
    {

    }

    class Collection
    {
        /**
         * Append items onto the end of the collection.
         *
         * @param iterable $items
         * @return $this
         */
        public function append($items)
        {
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
            return new Collection();
        }

        /**
         * Remove elements with given values from collection.
         *
         * @param iterable $values
         * @return $this
         */
        public function remove($values)
        {
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
            return $this;
        }
    }
}

namespace Illuminate\Contracts\Auth {

    /**
     * @mixin \App\Models\User
     * @mixin \App\User
     */
    interface Authenticatable
    {

    }
}
