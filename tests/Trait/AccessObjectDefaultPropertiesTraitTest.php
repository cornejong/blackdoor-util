<?php

namespace Tests\Trait;

use Blackdoor\Testing\AbstractTest;
use Blackdoor\Util\Traits\AccessObjectDefaultPropertyValues;
use ReflectionProperty;

class AccessObjectDefaultPropertiesTraitTest extends AbstractTest
{
    public $object;

    public function init()
    {
    }

    public function testGetDefaultPropertyValues()
    {
        $this->assert(TraitParent::getDefaultPropertyValues() === [
            "publicStatic" => "publicStatic",
            "privateStatic" => "privateStatic",
            "protectedStatic" => "protectedStatic",
            "public" => "public",
            "private" => "private",
            "protected" => "protected",
        ]);
    }

    public function testGetDefaultPropertyValue()
    {
        $this->assert(TraitParent::getDefaultPropertyValue('protectedStatic') === 'protectedStatic1');
    }

    public function testFilteredGetDefaultPropertyValues()
    {
        $this->assert(TraitParent::getDefaultPropertyValues(\ReflectionProperty::IS_STATIC), [
            "publicStatic" => "publicStatic",
            "privateStatic" => "privateStatic",
            "protectedStatic" => "protectedStatic",
        ], 'OnlyStaticProperties');

        $this->assert(TraitParent::getDefaultPropertyValues(\ReflectionProperty::IS_PUBLIC), [
            "publicStatic" => "publicStatic",
            "public" => "public",
        ], 'OnlyPublicProperties');

        $this->assert(TraitParent::getDefaultPropertyValues(\ReflectionProperty::IS_PRIVATE), [
            "privateStatic" => "privateStatic",
            "protectedStatic" => "protectedStatic",
            "private" => "private",
            "protected" => "protected",
        ], 'OnlyPrivateProperties');

        /* $this->assert(TraitParent::getDefaultPropertyValues(\ReflectionProperty::IS_PROTECTED), [
            "protectedStatic" => "protectedStatic",
            "protected" => "protected",
        ], 'OnlyProtectedProperties'); */
    }
}

class TraitParent
{
    use AccessObjectDefaultPropertyValues;

    public static $publicStatic = 'publicStatic';
    private static $privateStatic = 'privateStatic';
    private static $protectedStatic = 'protectedStatic';

    public $public = 'public';
    private $private = 'private';
    private $protected = 'protected';
}
