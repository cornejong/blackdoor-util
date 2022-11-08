<?php

namespace Blackdoor\Util\Objects;

use IteratorAggregate;
use Exception;
use Countable;
use ArrayAccess;

use Blackdoor\Util\Json;
use Blackdoor\Util\Arr;

class Collection implements ArrayAccess, IteratorAggregate, Countable
{
    public const STATUS_NEW = 0;
    public const STATUS_LOADED = 1;

    private $data = [];
    private $status = self::STATUS_NEW;

    public function __construct(array $data = [])
    {
        if (!empty($data)) {
            $this->load($data);
        }
    }

    public function load(array $data): bool
    {
        $this->status = self::STATUS_LOADED;
        $data = Arr::sanitize($data);
        return ($this->data = $data) ? true : false;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function statusIs(int $status): bool
    {
        return $status === $this->status ? true : false;
    }

    public function isNew(): bool
    {
        return ($this->status == self::STATUS_NEW) ? true : false;
    }

    public function isLoaded(): bool
    {
        return ($this->status == self::STATUS_LOADED) ? true : false;
    }

    public function reset(): bool
    {
        return ($this->data = []) ? true : false;
    }

    public function offsetSet($offset, $value)
    {
        if ($offset) {
            return ($this->data[$offset] = $value) ? true : false;
        } else {
            return ($this->data[] = $value) ? true : false;
        }
    }

    public function offsetExists($offset): bool
    {
        return isset($this->data[$offset]) ? true : false;
    }

    public function offsetUnset($offset): void
    {
        unset($this->data[$offset]);
    }

    public function offsetGet($offset)
    {
        return ($this->offsetExists($offset)) ? $this->data[$offset] : null;
    }

    public function getIterator(): IteratorObject
    {
        return new IteratorObject($this->data);
    }

    public function count(): int
    {
        return count($this->data);
    }

    public function countAll(): int
    {
        return Arr::recursiveCount($this->data);
    }

    public function contains(string $element, bool $strict = true): bool
    {
        return in_array($element, $this->data, $strict) ? true : false;
    }

    public function get(string $query)
    {
        return Arr::get($query, $this->data);
    }

    public function search(string $query, &$found, bool $strict = false)
    {
        return Arr::searchByQuery($query, $this->data, $found, $strict);
    }

    public function asJson()
    {
        return Json::prettyEncode($this->data);
    }

    public function asArray()
    {
        return Arr::sanitize($this->data);
    }

    public function hibernate()
    {
        return serialize($this);
    }
}
