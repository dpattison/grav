<?php
namespace Grav\Common;

use Grav\Component\ArrayTraits\Constructor;
use Grav\Component\ArrayTraits\ArrayAccessWithGetters;
use Grav\Component\ArrayTraits\Iterator as ArrayIterator;
use Grav\Component\ArrayTraits\Countable;
use Grav\Component\ArrayTraits\Serializable;
use Grav\Component\ArrayTraits\Export;

/**
 * Class Iterator
 * @package Grav\Common
 */
class Iterator implements \ArrayAccess, \Iterator, \Countable, \Serializable
{
    use Constructor, ArrayAccessWithGetters, ArrayIterator, Countable, Serializable, Export;

    /**
     * @var array
     */
    protected $items;

    /**
     * Convert function calls for the existing keys into their values.
     *
     * @param  string $key
     * @param  mixed  $args
     * @return mixed
     */
    public function __call($key, $args)
    {
        return (isset($this->items[$key])) ? $this->items[$key] : null;
    }

    /**
     * Clone the iterator.
     */
    public function __clone()
    {
        foreach ($this as $key => $value) {
            if (is_object($value)) {
                $this->$key = clone $this->$key;
            }
        }
    }

    /**
     * Convents iterator to a comma separated list.
     *
     * @return string
     * @todo Add support to nested sets.
     */
    public function __toString()
    {
        return implode(',', $this->items);
    }

    /**
     * Remove item from the list.
     *
     * @param $key
     */
    public function remove($key)
    {
        $this->offsetUnset($key);
    }

    /**
     * Return previous item.
     *
     * @return mixed
     */
    public function prev()
    {
        return prev($this->items);
    }

    /**
     * Return nth item.
     *
     * @param int $key
     * @return mixed|bool
     */
    public function nth($key)
    {
        $items = array_values($this->items);
        return (isset($items[$key])) ? $this->offsetGet($items[$key]) : false;
    }

    /**
     * @param mixed $needle Searched value.
     * @return string|bool  Key if found, otherwise false.
     */
    public function indexOf($needle)
    {
        foreach (array_values($this->items) as $key => $value) {
            if ($value === $needle) {
                return $key;
            }
        }
        return false;
    }

    /**
     * Shuffle items.
     *
     * @return $this
     */
    public function shuffle()
    {
        $keys = array_keys($this->items);
        shuffle($keys);

        $new = array();
        foreach($keys as $key) {
            $new[$key] = $this->items[$key];
        }

        $this->items = $new;

        return $this;
    }

    /**
     * Slice the list.
     *
     * @param int $offset
     * @param int $length
     * @return $this
     */
    public function slice($offset, $length = null)
    {
        $this->items = array_slice($this->items, $offset, $length);

        return $this;
    }

    /**
     * Pick one or more random entries.
     *
     * @param int $num  Specifies how many entries should be picked.
     * @return $this
     */
    public function random($num = 1)
    {
        $this->items = array_intersect_key($this->items, array_flip((array) array_rand($this->items, $num)));

        return $this;
    }

    /**
     * Append new elements to the list.
     *
     * @param array|Iterator $items  Items to be appended. Existing keys will be overridden with the new values.
     * @return $this
     */
    public function append($items)
    {
        if ($items instanceof static) {
            $items = $items->toArray();
        }
        $this->items = array_merge($this->items, (array) $items);

        return $this;
    }
}
