<?php

/*
 * File: IteratorAccess.php
 * Project: Blackdoor\Util\Traits
 * File Created: Tuesday, 24th March 2020
 * Author: Corné de Jong (corne@tearo.eu)
 * ------------------------------------------------------------
 * Copyright 2019 - 2020 SouthCoast
 */

namespace Blackdoor\Util\Traits;

/**
 * Trait providing all interfacing methods to implement Iterator
 *
 * Required is to specify a container pointer:
 * - protected $containerPointer = '{The name of the variable that you want to iterate over}';
 */
trait IteratorAccess
{
    protected $iteratorPosition = 0;

    public function rewind()
    {
        if (empty($this->containerPointer ?? null)) {
            throw new Exception('No container pointer provided!', 1);
        }

        $this->iteratorPosition = 0;
    }

    public function current()
    {
        if (empty($this->containerPointer ?? null)) {
            throw new Exception('No container pointer provided!', 1);
        }

        return $this->{$this->containerPointer}[$this->iteratorPosition];
    }

    public function key()
    {
        if (empty($this->containerPointer ?? null)) {
            throw new Exception('No container pointer provided!', 1);
        }

        return $this->iteratorPosition;
    }

    public function next()
    {
        if (empty($this->containerPointer ?? null)) {
            throw new Exception('No container pointer provided!', 1);
        }

        $this->iteratorPosition++;
    }

    public function valid()
    {
        if (empty($this->containerPointer ?? null)) {
            throw new Exception('No container pointer provided!', 1);
        }

        return isset($this->{$this->containerPointer}[$this->iteratorPosition]);
    }
}
