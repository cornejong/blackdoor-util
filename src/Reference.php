<?php

namespace Blackdoor\Util;

class Reference
{
    public $data;

    public function __construct(&$data)
    {
        $this->data = &$data;
    }

    /**
     * create
     *
     * @param mixed $data
     * @return self
     */
    public static function &create(&$data)
    {
        return $data instanceof static
            ? $data
            : new self($data);
    }

    /**
     * get value
     *
     * @param mixed $reference
     * @return mixed
     */
    public static function &get(&$reference)
    {
        return $reference instanceof static
            ? $reference->data
            : $reference;
    }
}
