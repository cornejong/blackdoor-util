<?php

/*
 * File: MagicObjectAccess.php
 * Project: Blackdoor\Util\Traits
 * File Created: Tuesday, 24th March 2020 8:55:52 pm
 * Author: Corné de Jong (corne@tearo.eu)
 * ------------------------------------------------------------
 * Copyright 2019 - 2020 SouthCoast
 */

namespace Blackdoor\Util\Traits;

/**
 * Trait implementing a simple version of __get,__set, __isset & __unset
 *
 * Required is to specify a container pointer:
 * - protected $containerPointer = '{The name of the variable that you want to access}';
 *
 * Also it will check if there are getters or setters defined for the offset.
 * Additionally it will check for 'has{Offset}' and 'unset{Offset}' methods in __isset and __unset respectively
 */
trait MagicObjectAccessWithGettersAndSetters
{
    // protected $containerPointer;

    /**
     * Get a data by offset
     *
     * @param string The key data to retrieve
     * @access public
     */
    public function __get($offset)
    {
        if (empty($this->containerPointer ?? null)) {
            throw new \Exception('No container pointer provided!', 1);
        }

        $getter = 'get' . ucfirst($offset);
        if (method_exists($this, $getter)) {
            return call_user_func([$this, $getter]);
        }

        return $this->{$this->containerPointer}[$offset] ?? null;
    }

    /**
     * Assigns a value to the specified data
     *
     * @param string The data key to assign the value to
     * @param mixed  The value to set
     * @access public
     */
    public function __set($offset, $value)
    {
        if (empty($this->containerPointer ?? null)) {
            throw new \Exception('No container pointer provided!', 1);
        }

        $setter = 'set' . ucfirst($offset);
        if (method_exists($this, $setter)) {
            return call_user_func([$this, $setter], $value);
        }

        return $this->{$this->containerPointer}[$offset] = $value;
    }

    /**
     * Whether or not an data exists by key
     *
     * @param string An data key to check for
     * @access public
     * @return boolean
     */
    public function __isset($offset)
    {
        if (empty($this->containerPointer ?? null)) {
            throw new \Exception('No container pointer provided!', 1);
        }

        $method = 'has' . ucfirst($offset);
        if (method_exists($this, $method)) {
            return call_user_func([$this, $method]);
        }

        return isset($this->{$this->containerPointer}[$offset]);
    }

    /**
     * Unsets an data by key
     *
     * @param string The key to unset
     * @access public
     */
    public function __unset($offset)
    {
        if (empty($this->containerPointer ?? null)) {
            throw new \Exception('No container pointer provided!', 1);
        }

        $method = 'unset' . ucfirst($offset);
        if (method_exists($this, $method)) {
            return call_user_func([$this, $method]);
        }

        unset($this->{$this->containerPointer}[$offset]);
    }
}
