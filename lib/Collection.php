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