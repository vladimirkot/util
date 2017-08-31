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

namespace Granule\Util\Tree;

class MutableArrayTree extends ArrayTree {
    public function offsetSet($offset, $value): void {
        if (is_null($offset)) {
            $this->data[] = $value;
        } else {
            $offset = $this->extractKey($offset);
            $data = &$this->data;

            foreach ($offset as $key) {
                if (!array_key_exists($key, $data)) {
                    $data[$key] = [];
                }

                $data = &$data[$key];
            }

            $data = $value;
        }
    }

    public function offsetUnset($offset): void {
        $offset = $this->extractKey($offset);
        $this->find($this->data, array_shift($offset), [], $offset,
            function (&$value, array $p, &$data, string $key) {
                unset($data[$key]);
            }, function (array $path) {
                throw new \OutOfBoundsException(sprintf('Element "%s" not found', implode('.', $path)));
            });
    }

    public function toMutable(): MutableArrayTree {
        return $this;
    }

    public function toImmutable(): ArrayTree {
        return ArrayTree::fromArray($this->data);
    }
}