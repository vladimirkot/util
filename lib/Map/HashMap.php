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

use Granule\Util\{
    Collection, Hashable, Map,
    StrictTypedKey, StrictTypedValue, TypeHelper
};

abstract class HashMap implements Map {
    /** @var mixed[] */
    protected $keys = [];
    /** @var mixed[] */
    protected $values = [];

    public function __construct(HashMapBuilder $builder) {
        $this->keys = $builder->getKeys();
        $this->values = $builder->getValues();
    }

    public static function builder(): HashMapBuilder {
        $keyType = $valueType = null;
        $reflection = new \ReflectionClass(static::class);
        /** @var StrictTypedValue|StrictTypedKey|HashMap $fake */
        $fake = $reflection->newInstanceWithoutConstructor();

        if (is_a(static::class, StrictTypedValue::class, true)) {
            $valueType = $fake->getValueType();
        }
        if (is_a(static::class, StrictTypedKey::class, true)) {
            $keyType = $fake->getKeyType();
        }

        return new HashMapBuilder(function ($k) use ($fake) {
            return $fake->getKeyHash($k);
        }, static::class, $valueType, $keyType);
    }

    /** {@inheritdoc} */
    public function current() {
        return current($this->values);
    }

    /** {@inheritdoc} */
    public function next(): void {
        next($this->keys);
        next($this->values);
    }

    /** {@inheritdoc} */
    public function key() {
        return current($this->keys);
    }

    /** {@inheritdoc} */
    public function valid(): bool {
        return key($this->keys) !== null;
    }

    /** {@inheritdoc} */
    public function rewind(): void {
        reset($this->keys);
        reset($this->values);
    }

    /** {@inheritdoc} */
    public function offsetExists($offset): bool {
        return in_array($offset, $this->keys);
    }

    /** {@inheritdoc} */
    public function offsetGet($offset) {
        TypeHelper::validateKey($offset, $this);

        return $this->values[$this->getKeyHash($offset)];
    }

    /** {@inheritdoc} */
    public function offsetSet($offset, $value) {
        throw new \BadMethodCallException('Unable to change immutable map');
    }

    /** {@inheritdoc} */
    public function offsetUnset($offset): void {
        throw new \BadMethodCallException('Unable to change immutable map');
    }

    /** {@inheritdoc} */
    public function hash(): string {
        return $this->hashKey($this);
    }

    /** {@inheritdoc} */
    public function containsKey($key): bool {
        return in_array($key, $this->keys);
    }

    /** {@inheritdoc} */
    public function containsValue($value): bool {
        return in_array($value, $this->values);
    }

    /** {@inheritdoc} */
    public function equals(Map $map): bool {
        return $this->hash() === $map->hash();
    }

    /** {@inheritdoc} */
    public function filter(callable $filter): Map {
        $builder = static::builder();
        foreach ($this as $key => $element) {
            if ($filter($element, $key)) {
                $builder->add($key, $element);
            }
        }

        return $builder->build();
    }

    /** {@inheritdoc} */
    public function get($key) {
        TypeHelper::validateKey($key, $this);
        $hash = $this->getKeyHash($key);

        return array_key_exists(
            $hash,
            $this->values
        ) ? $this->values[$hash] : null;
    }

    /** {@inheritdoc} */
    public function getOrDefault($key, $defaultValue) {
        TypeHelper::validateKey($key, $this);
        TypeHelper::validateValue($defaultValue, $this);

        return $this->get($key) ?: $defaultValue;
    }

    /** {@inheritdoc} */
    public function isEmpty(): bool {
        return $this->count() === 0;
    }

    /** {@inheritdoc} */
    public function keys(): array {
        return array_values($this->keys);
    }

    /** {@inheritdoc} */
    public function count(): int {
        return count($this->values);
    }

    /** {@inheritdoc} */
    public function values(string $collectionClass = null): Collection {
        return call_user_func([$collectionClass ?: Collection\ArrayCollection::class, 'fromArray'], $this->toArray());
    }

    /** {@inheritdoc} */
    public function toArray(): array {
        return array_values($this->values);
    }

    private function getKeyHash($key): string {
        if ($key instanceof Hashable) {
            return $key->hash();
        }

        return $this->hashKey($key);
    }

    abstract protected function hashKey($key): string;
}