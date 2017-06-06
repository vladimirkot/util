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

interface MutableMap extends Map {
    /** Removes all of the mappings from this map (optional operation). */
    function clear(): void;
    /** Performs the given action for each entry in this map until all entries
     * have been processed or the action throws an exception. */
    function forEachApply(callable $callback): void;
    /** Associates the specified value with the specified key in this map. */
    function put($key, $value);
    /** Copies all of the mappings from the specified map to this map. */
    function putAll(Map $map): void;
    /** If the specified key is not already associated with a value
     * (or is mapped to null) associates it with the given value and returns null,
     * else returns the current value. */
    function putIfAbsent($key, $value): void;
    /** Removes the mapping for a key from this map if it is present. */
    function remove($key);
    /** Removes the entry for the specified key only if it is currently mapped
     * to the specified value. */
    function removeIfEqual($key, $value): bool;
    /** Replaces the entry for the specified key only if it is currently mapped to some value. */
    function replace($key, $value);
    /** Replaces the entry for the specified key only if currently mapped to the specified value.*/
    function replaceIfEqual($key, $oldValue, $newValue): bool;
    /** Replaces each entry's value with the result of invoking the given function
     * on that entry until all entries have been processed or the function throws an exception. */
    function replaceAll(callable $replacer): void;
}