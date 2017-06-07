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

class MutableArrayCollection extends ArrayCollection implements MutableCollection {

    /** {@inheritdoc} */
    public function add($element): bool {
        $this->validateElement($element);
        if ($this->contains($element)) {
            return false;
        }
        $this->elements[] = $element;

        return true;
    }

    /** {@inheritdoc} */
    public function addAll(Collection $collection): bool {
        $changed = false;
        foreach ($collection as $item) {
            if ($this->add($item)) {
                $changed = true;
            }
        }

        return $changed;
    }

    /** {@inheritdoc} */
    public function clear(): void {
        $this->elements = [];
    }

    /** {@inheritdoc} */
    public function remove($element): bool {
        $index = $this->indexOf($element);
        if ($index !== null) {
            unset($this->elements[$index]);

            return true;
        }

        return false;
    }

    /** {@inheritdoc} */
    public function removeAll(Collection $collection): bool {
        $changed = false;
        foreach ($collection as $element) {
            if ($this->remove($element)) {
                $changed = true;
            }
        }

        return $changed;
    }

    /** {@inheritdoc} */
    public function removeIf(callable $filter): bool {
        $count = count($this->elements);
        $this->elements = array_filter($this->elements, function ($v, $k) use ($filter) {
            return !$filter($v, $k);
        }, ARRAY_FILTER_USE_BOTH);

        return $count !== count($this->elements);
    }

    /** {@inheritdoc} */
    public function retainAll(Collection $collection): bool {
        $changed = false;
        $retained = $collection->toArray();

        foreach ($this->elements as $key => $element) {
            if (!in_array($element, $retained)) {
                unset($this->elements[$key]);
                $changed = true;
            }
        }

        return $changed;
    }

    public function offsetUnset($offset) {
        if (!is_integer($offset)) {
            throw new \TypeError(
                sprintf('Expected integer type, provided: %s', gettype($offset))
            );
        }

        unset($this->elements[$offset]);
    }
}