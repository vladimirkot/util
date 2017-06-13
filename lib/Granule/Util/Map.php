<?php
/*
 * MIT License
 *
 * Copyright (c) 2017 Eugene Bogachov
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace Granule\Util;

interface Map extends \Iterator, \Countable, \ArrayAccess, Hashable {
    /** Returns true if this map contains a mapping for the specified key. */
    function containsKey($key): bool;
    /** Returns true if this map maps one or more keys to the specified value. */
    function containsValue($value): bool;
    /** Compares the specified object with this map for equality. */
    function equals(Map $map): bool;
    /** Returns filtered map */
    function filter(callable $filter): Map;
    /** Returns the value to which the specified key is mapped,
     * or null if this map contains no mapping for the key. */
    function get($key);
    /** Returns the value to which the specified key is mapped,
     * or defaultValue if this map contains no mapping for the key. */
    function getOrDefault($key, $defaultValue);
    /** Returns true if this map contains no key-value mappings. */
    function isEmpty(): bool;
    /** Returns an array of the keys contained in this map. */
    function keys(): array;
    /** Returns the number of key-value mappings in this map. */
    function count(): int;
    /** Returns a Collection view of the values contained in this map. */
    function values(string $collectionClass = null): Collection;
    /** Returns array representation of map */
    function toArray(): array;
    /**
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     */
    function current();
    /**
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     */
    function next(): void;
    /**
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     */
    function key();
    /**
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     */
    function valid(): bool;
    /**
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     */
    function rewind(): void;
}