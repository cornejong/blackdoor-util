<?php

/*
 * File: ArrayAccess.php
 * Project: Blackdoor\Util\Traits
 * File Created: Tuesday, 24th March 2020 8:23:11 pm
 * Author: Corné de Jong (corne@tearo.eu)
 * ------------------------------------------------------------
 * Last Modified: Tuesday, 24th March 2020 9:20:16 pm
 * Modified By: Corné de Jong (corne@tearo.eu>)
 * ------------------------------------------------------------
 * Copyright 2019 - 2020 SouthCoast
 */

namespace Blackdoor\Util\Traits;

use Exception;

/**
 * Trait providing all interfacing methods to implement ArrayAccess
 *
 * Required is to specify a container pointer:
 * - protected $containerPointer = '{The name of the variable that you want to access via ArrayAccess}';
 */
trait ArrayAccess
{
    /**
     * Assigns a value to the specified offset
     *
     * @param string The offset to assign the value to
     * @param mixed  The value to set
     * @access public
     * @abstracting ArrayAccess
     */
    public function offsetSet($offset, $value)
    {
        if (!property_exists($this, 'containerPointer') || empty($this->containerPointer)) {
            throw new Exception(self::class . ': Container pointer not set or empty!', 1);
        }

        return is_null($offset) ? $this->{$this->containerPointer}[] = $value : $this->{$this->containerPointer}[$offset] = $value;
    }

    /**
     * Whether or not an offset exists
     *
     * @param string An offset to check for
     * @access public
     * @return boolean
     * @abstracting ArrayAccess
     */
    public function offsetExists($offset)
    {
        if (!property_exists($this, 'containerPointer') || empty($this->containerPointer)) {
            throw new Exception(self::class . ': Container pointer not set or empty!', 1);
        }

        return isset($this->{$this->containerPointer}[$offset]);
    }

    /**
     * Unsets an offset
     *
     * @param string The offset to unset
     * @access public
     * @abstracting ArrayAccess
     */
    public function offsetUnset($offset)
    {
        if (!property_exists($this, 'containerPointer') || empty($this->containerPointer)) {
            throw new Exception(self::class . ': Container pointer not set or empty!', 1);
        }

        unset($this->{$this->containerPointer}[$offset]);
    }

    /**
     * Returns the value at specified offset
     *
     * @param string The offset to retrieve
     * @access public
     * @return mixed
     * @abstracting ArrayAccess
     */
    public function offsetGet($offset)
    {
        if (!property_exists($this, 'containerPointer') || empty($this->containerPointer)) {
            throw new Exception(self::class . ': Container pointer not set or empty!', 1);
        }

        return $this->{$this->containerPointer}[$offset] ?? null;
    }

    /**
     * Whether or not an data exists by key
     *
     * @param string An data key to check for
     * @access public
     * @return boolean
     * @abstracting ArrayAccess
     */
    public function __isset($offset)
    {
        if (!property_exists($this, 'containerPointer') || empty($this->containerPointer)) {
            throw new Exception(self::class . ': Container pointer not set or empty!', 1);
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
        if (!property_exists($this, 'containerPointer') || empty($this->containerPointer)) {
            throw new Exception(self::class . ': Container pointer not set or empty!', 1);
        }

        unset($this->{$this->containerPointer}[$offset]);
    }
}
