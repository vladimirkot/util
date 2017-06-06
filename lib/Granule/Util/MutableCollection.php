<?php

namespace Granule\Util;

interface MutableCollection extends Collection {
    /** Ensures that this collection contains the specified element (optional operation). */
    function add($element): bool;
    /** Adds all of the elements in the specified collection to this collection (optional operation). */
    function addAll(Collection $collection): bool;
    /** Removes all of the elements from this collection (optional operation). */
    function clear(): void;
    /** Removes a single instance of the specified element from this collection, if it is present (optional operation). */
    function remove($element): bool;
    /** Removes all of this collection's elements that are also contained in the specified collection (optional operation). */
    function removeAll(Collection $collection): bool;
    /** Removes all of the elements of this collection that satisfy the given predicate. */
    function removeIf(callable $filter): bool;
    /** Retains only the elements in this collection that are contained in the specified collection (optional operation). */
    function retainAll(Collection $collection): bool;
}