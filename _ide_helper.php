<?php

namespace Illuminate\Database\Schema {

    use Febalist\Laravel\Support\Macro;

    /** @mixin Macro */
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
}
