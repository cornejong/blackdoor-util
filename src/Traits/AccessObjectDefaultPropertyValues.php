<?php declare(strict_types = 1);


namespace Blackdoor\Util\Traits;

/**
 * This trait creates static methods to access the default value properties of the parent class
 */
trait AccessObjectDefaultPropertyValues
{

    /**
     * Returns all default values for object
     *
     * @param int|null $filter  Accepted: 
     *  - ReflectionProperty::IS_STATIC
     *  - ReflectionProperty::IS_PUBLIC
     *  - ReflectionProperty::IS_PROTECTED
     *  - ReflectionProperty::IS_PRIVATE
     *
     * @return array
     */
    public static function getDefaultPropertyValues(int $filter = null) : array
    { 
        $out = [];
        $reflect = new \ReflectionClass(static::class);
        foreach($reflect->getProperties($filter) as $property) {
            $out[$property->getName()] = $property->getDefaultValue();
        }

        return $out;
    }

    /**
     * Get the default value of property
     *
     * @param string $name  name of the property
     *
     * @return mixed
     */
    public static function getDefaultPropertyValue(string $name) : mixed
    {
        $reflect = new \ReflectionClass(static::class);
        if(!$reflect->hasProperty($name)) {
            throw new \Exception("Unknown Property '$name'.", 1);
        }

        return $reflect->getProperty($name)->getDefaultValue();
    }
}
