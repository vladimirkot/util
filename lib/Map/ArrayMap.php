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

use Granule\Util\Collection;
use Granule\Util\Collection\ArrayCollection;
use Granule\Util\Map;
use Granule\Util\StrictTypedKey;
use Granule\Util\StrictTypedValue;
use Granule\Util\TypeHelper;

class ArrayMap implements Map {
    /** @var object[] */
    protected $elements = [];

    public function __construct(MapBuilder $builder) {
        $this->elements = $builder->getElements();
    }

    public static function fromArray(array $elements): Map {
        $builder = self::builder();

        foreach ($elements as $key => $value) {
            $builder->add($key, $value);
        }

        return $builder->build();
    }

    public static function builder(): MapBuilder {
        $keyType = $valueType = null;
        if (is_a(static::class, StrictTypedKey::class, true)
                || is_a(static::class, StrictTypedValue::class, true)
        ) {
            $reflection = new \ReflectionClass(static::class);
            /** @var StrictTypedValue|StrictTypedKey $fake */
            $fake = $reflection->newInstanceWithoutConstructor();

            if (is_a(static::class, StrictTypedValue::class, true)) {
                $valueType = $fake->getValueType();
            }
            if (is_a(static::class, StrictTypedKey::class, true)) {
                $keyType = $fake->getKeyType();
            }
        }

        return new MapBuilder(static::class, $valueType, $keyType);
    }

    /** {@inheritdoc} */
    public function containsKey($key): bool {
        return array_key_exists($key, $this->elements);
    }

    /** {@inheritdoc} */
    public function containsValue($value): bool {
        return in_array($value, $this->elements);
    }

    /** {@inheritdoc} */
    public function equals(Map $map): bool {
        return $map->toArray() == $this->toArray();
    }

    /** {@inheritdoc} */
    public function filter(callable $filter): Map {
        return static::fromArray(array_filter(
            $this->elements, $filter, ARRAY_FILTER_USE_BOTH
        ));
    }

    /** {@inheritdoc} */
    public function get($key) {
        TypeHelper::validateKey($key, $this);
        if ($this->containsKey($key)) {
            return $this->elements[$key];
        }

        return null;
    }

    /** {@inheritdoc} */
    public function getOrDefault($key, $defaultValue) {
        TypeHelper::validateKey($key, $this);
        if ($this->containsKey($key)) {
            return $this->elements[$key];
        }

        return $defaultValue;
    }

    /** {@inheritdoc} */
    public function isEmpty(): bool {
        return $this->count() === 0;
    }

    /** {@inheritdoc} */
    public function keys(): array {
        return array_keys($this->elements);
    }

    /** {@inheritdoc} */
    public function count(): int {
        return count($this->elements);
    }

    /** {@inheritdoc} */
    public function values(string $collectionClass = null): Collection {
        return call_user_func([
            $collectionClass ?: ArrayCollection::class, 'fromArray'
        ], $this->toArray());
    }

    /** {@inheritdoc} */
    public function toArray(): array {
        return $this->elements;
    }

    /** {@inheritdoc} */
    public function hash(): string {
        return md5(serialize($this->elements));
    }

    /** {@inheritdoc} */
    public function offsetExists($offset): bool {
        return $this->containsKey($offset);
    }

    /** {@inheritdoc} */
    public function offsetGet($offset) {
        return $this->get($offset);
    }

    /** {@inheritdoc} */
    public function offsetSet($offset, $value): void {
        throw new \BadMethodCallException('Unable to change immutable map');
    }

    /** {@inheritdoc} */
    public function offsetUnset($offset): void {
        throw new \BadMethodCallException('Unable to change immutable map');
    }

    /** {@inheritdoc} */
    public function current() {
        return current($this->elements);
    }

    /** {@inheritdoc} */
    public function next(): void {
        next($this->elements);
    }

    /** {@inheritdoc} */
    public function key() {
        return key($this->elements);
    }

    /** {@inheritdoc} */
    public function valid(): bool {
        return key($this->elements) !== null;
    }

    /** {@inheritdoc} */
    public function rewind(): void {
        reset($this->elements);
    }
}