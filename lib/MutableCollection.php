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