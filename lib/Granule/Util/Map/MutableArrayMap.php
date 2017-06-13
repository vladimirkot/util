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

namespace Granule\Util\Map;

use Granule\Util\MutableMap;

class MutableArrayMap extends ArrayMap implements MutableMap {
    /** {@inheritdoc} */
    public function clear(): void {
        $this->elements = [];
    }

    /** {@inheritdoc} */
    public function forEachApply(callable $callback): void {
        foreach ($this->elements as $key => $value) {
            $callback($value, $key);
        }
    }

    /** {@inheritdoc} */
    public function put($key, $value) {
        TypeHelper::validateKey($key, $this);
        TypeHelper::validateValue($value, $this);
        $oldValue = null;
        if ($this->containsKey($key)) {
            $oldValue = $this->get($key);
        }
        $this->elements[$key] = $value;

        return $oldValue;
    }

    /** {@inheritdoc} */
    public function putAll(Map $map): void {
        foreach ($map as $key => $value) {
            $this->put($key, $value);
        }
    }

    /** {@inheritdoc} */
    public function putIfAbsent($key, $value): void {
        if (!$this->containsKey($key)) {
            $this->put($key, $value);
        }
    }

    /** {@inheritdoc} */
    public function remove($key) {
        $value = $this->get($key);
        if ($value !== null) {
            unset($this->elements[$key]);
        }

        return $value;
    }

    /** {@inheritdoc} */
    public function removeIfEqual($key, $value): bool {
        $actualValue = $this->get($key);
        if ($actualValue === $value) {
            unset($this->elements[$key]);

            return true;
        }

        return false;
    }

    /** {@inheritdoc} */
    public function replace($key, $value) {
        if ($this->containsKey($key)) {
            return $this->put($key, $value);
        } else {
            return null;
        }
    }

    /** {@inheritdoc} */
    public function replaceIfEqual($key, $oldValue, $newValue): bool {
        $value = $this->get($key);
        if ($oldValue === $value) {
            $this->put($key, $newValue);

            return true;
        } else {
            return false;
        }
    }

    /** {@inheritdoc} */
    public function replaceAll(callable $replacer): void {
        foreach ($this as $key => $value) {
            $this->put($key, $replacer($value, $key));
        }
    }
}