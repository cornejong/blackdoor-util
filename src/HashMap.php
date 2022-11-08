<?php

namespace Blackdoor\Util;

class HashMap
{
    public function __construct(protected $map = [], protected $hashLength = 8)
    {
        # code...
    }

    public function add($data, bool $override = false) : string|bool
    {
        $hash = $this->hash($data);

        if ($this->contains(hash: $hash) && !$override) {
            return false;
        }

        $this->map[$hash] = $data;
        return $hash;
    }

    public function contains(string $hash = null, $data = null): bool
    {
        if (!$hash && !$data) {
            throw new \InvalidArgumentException("HashMap->contains() requires either 'hash' or 'data' to be passed. None provided.", 1);
        }

        return array_key_exists($hash ?? $this->hash($data), $this->map);
    }

    public function get(string $hash)
    {
        if (!$this->contains(hash: $hash)) {
            return false;
        }

        return $this->map[$hash];
    }

    public function hash($data): string
    {
        $string = "";

        switch (gettype($data)) {
            case 'object':
            case 'array':
                // $string = spl_object_hash($data);
                $string = serialize($data);
                break;

            default:
                $string = strval($data);
                break;
        }

        return $this->generateHash(
            input: $string,
            length: $this->hashLength
        );
    }

    protected function generateHash(string $input, int $length = 8): string
    {
        return substr(rtrim(base64_encode(hash('sha256', $input, true)), '='), 0, $length);
    }
}
