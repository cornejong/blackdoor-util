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
 */
trait MagicObjectAccess
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

        if (!isset($this->{$this->containerPointer}[$offset])) {
            trigger_error("Undefined property: " . static::class . "::$offset", E_USER_WARNING);
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

        unset($this->{$this->containerPointer}[$offset]);
    }
}
