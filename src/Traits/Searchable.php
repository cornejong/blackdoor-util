<?php

/*
 * File: Searchable.php
 * Project: Blackdoor\Util\Traits
 * File Created: Tuesday, 24th March 2020 8:38:07 pm
 * Author: Corné de Jong (corne@tearo.eu)
 * ------------------------------------------------------------
 * Last Modified: Tuesday, 24th March 2020 9:19:38 pm
 * Modified By: Corné de Jong (corne@tearo.eu>)
 * ------------------------------------------------------------
 * Copyright 2019 - 2020 SouthCoast
 */

namespace Blackdoor\Util\Traits;

/**
 * Trait providing search related functionality to a class variable.
 *
 * Required is to specify a container pointer:
 * - protected $containerPointer = '{The name of the variable that you want to search}';
 */
trait Searchable
{
    public function contains($value)
    {
        if (empty($this->containerPointer ?? null)) {
            throw new Exception('No container pointer provided!', 1);
        }

        return array_key_exists($value, $this->{$this->containerPointer});
    }
}
