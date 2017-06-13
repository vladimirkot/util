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

use Granule\Util\TypeHelper;

class HashMapBuilder {
    /** @var array */
    protected $keys = [];
    /** @var array */
    protected $values = [];
    /** @var string */
    protected $mapClass;
    /** @var ?string */
    protected $mappingType;
    /** @var ?string */
    protected $keyType;
    /** @var callable */
    protected $hashFunc;

    public function __construct(
        callable $hashFunc, string $mapClass,
        ?string $mappingType, ?string $keyType
    ) {
        $this->hashFunc = $hashFunc;
        $this->mapClass = $mapClass;
        $this->mappingType = $mappingType;
        $this->keyType = $keyType;
    }

    public function add($key, $value): HashMapBuilder {
        if ($this->keyType) {
            TypeHelper::validate($key, $this->keyType);
        }

        if ($this->mappingType) {
            TypeHelper::validate($value, $this->mappingType);
        }

        $hashFunc = $this->hashFunc;
        $hash = $hashFunc($key);

        $this->keys[$hash] = $key;
        $this->values[$hash] = $value;

        return $this;
    }

    public function getKeys(): array {
        return $this->keys;
    }

    public function getValues(): array {
        return $this->values;
    }

    public function build(): HashMap {
        $class = $this->mapClass;

        return new $class($this);
    }
}