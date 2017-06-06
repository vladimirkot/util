<?php

namespace Granule\Util;

/**
 * Interface Map
 * @see http://docs.oracle.com/javase/8/docs/api/java/util/Map.html
 */
interface Map extends \IteratorAggregate, Hashable {
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
    function size(): int;
    /** Returns a Collection view of the values contained in this map. */
    function values(string $collectionClass): Collection;
    /** Returns array representation of map */
    function toArray(): array;
}