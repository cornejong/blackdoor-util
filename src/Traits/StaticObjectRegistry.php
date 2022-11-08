<?php

namespace Blackdoor\Util\Traits;

trait StaticObjectRegistry
{
    protected static $instances = [];

    public static function __staticCall($name, $arguments)
    {
        if (array_key_exists($name, self::$instances)) {
            return self::$instances[$name];
        }

        return null;
    }

    public function register(object $object)
    {
        self::$instances[basename(get_class($object))] = $object;
    }
}
