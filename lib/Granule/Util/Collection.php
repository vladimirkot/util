<?php

namespace Granule\Util;

/**
 * @see http://docs.oracle.com/javase/8/docs/api/java/util/Collection.html
 */
interface Collection extends \IteratorAggregate, \Countable, \ArrayAccess, Hashable {
    /** Returns true if this collection contains the specified element. */
    public function contains($element): bool;
    /** Returns true if this collection contains all of the elements in the specified collection. */
    public function containsAll(Collection $collection): bool;
    /** Compares the specified object with this collection for equality. */
    public function equals(Collection $collection): bool;
    /** Returns the element at the specified position in collection. */
    public function get(int $index);
    /** Returns the index of the first occurrence of the specified element in this list,
     *  or -1 if this list does not contain the element.
     */
    public function indexOf($element): ?int;
    /** Returns true if this collection contains no elements. */
    public function isEmpty(): bool;
    /** Returns the number of elements in this collection. */
    public function count(): int;
    /** Returns an array containing all of the elements in this collection. */
    public function toArray(): array;
    /** Returns an iterator over the elements in this collection. */
    public function getIterator(): \Iterator;
    /** {@inheritdoc} */
    public function offsetExists($offset): bool;
}