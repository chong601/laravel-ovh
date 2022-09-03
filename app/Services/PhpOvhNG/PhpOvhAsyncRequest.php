<?php

namespace App\Services\PhpOvhNG;

use ArrayAccess;

/**
 * A class that provides a clean way to create async requests with GuzzleHTTP
 *
 * PS: I don't know if this is a good approach over just using associative arrays... but hey, something needs to be done
 * or I won't get anything done, right?
 */
class PhpOvhAsyncRequest implements ArrayAccess
{
    private array $requestArray = [];
    /**
     * @inheritDoc
     */
    public function offsetExists($offset): bool
    {
        return isset($this->requestArray[$offset]);
    }

    /**
     * @inheritDoc
     */
    public function offsetGet($offset)
    {
        return $this->requestArray[$offset] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->requestArray[] = $value;
        } else {
            $this->requestArray[$offset] = $value;
        }
        return $value;
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset($offset)
    {
        unset($this->requestArray[$offset]);
    }

    public function toArray(): array
    {
        return $this->requestArray;
    }
}
