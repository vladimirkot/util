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
use Granule\Util\Map;
use Granule\Util\TypeHelper;
use SplObjectStorage;

class ObjectMap implements Map {
    /** @var SplObjectStorage */
    protected $elements;
    /** @var int */
    protected $index;

    protected function __construct(SplObjectStorage $elements) {
        $this->elements = $elements;
    }

    public static function fromStorage(SplObjectStorage $elements) {
        return new static($elements);
    }

    public static function createEmpty() {
        return new static(new SplObjectStorage());
    }

    public static function builder(): MapBuilder {
        $valueType = null;
        $reflection = new \ReflectionClass(static::class);
        /** @var StrictTypedValue|StrictTypedKey $fake */
        $fake = $reflection->newInstanceWithoutConstructor();
        if (is_a(static::class, StrictTypedValue::class, true)) {
            $valueType = $fake->getValueType();
        }

        return new ObjectMapBuilder(static::class, $valueType, $fake->getKeyType());
    }

    /** {@inheritdoc} */
    public function current() {
        return $this->elements->getInfo();
    }

    /** {@inheritdoc} */
    public function next(): void {
        $this->elements->next();
    }

    /** {@inheritdoc} */
    public function key() {
        return $this->elements->current();
    }

    /** {@inheritdoc} */
    public function valid(): bool {
        return $this->elements->valid();
    }

    /** {@inheritdoc} */
    public function rewind(): void {
        $this->elements->rewind();
    }

    /** {@inheritdoc} */
    public function containsKey($key): bool {
        TypeHelper::validateKey($key, $this);

        return $this->elements->contains($key);
    }

    /**
     * {@inheritdoc}
     * @deprecated Don't use as it a heavy one (because of @see \SplObjectStorage)
     */
    public function containsValue($value): bool {
        TypeHelper::validateValue($value, $this);
        foreach ($this as $v) {
            if ($value == $v) {
                return true;
            }
        }

        return false;
    }

    /** {@inheritdoc} */
    public function equals(Map $map): bool {
        if ($map instanceof ObjectMap) {
            return $map->hash() === $this->hash();
        }

        return false;
    }

    /** {@inheritdoc} */
    public function filter(callable $filter): Map {
        $newElements = new SplObjectStorage();
        foreach ($this as $key => $element) {
            if ($filter($element, $key)) {
                $newElements[$key] = $element;
            }
        }

        return static::fromStorage($newElements);
    }

    /** {@inheritdoc} */
    public function get($key) {
        if ($this->containsKey($key)) {
            return $this->elements[$key];
        }

        return null;
    }

    /** {@inheritdoc} */
    public function getOrDefault($key, $defaultValue) {
        TypeHelper::validateKey($key, $this);
        TypeHelper::validateValue($defaultValue, $this);
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
        $keys = [];
        foreach ($this as $key => $value) {
            $keys[] = $key;
        }

        return $keys;
    }

    /** {@inheritdoc} */
    public function count(): int {
        return $this->elements->count();
    }

    /** {@inheritdoc} */
    public function values(string $collectionClass = null): Collection {
        return call_user_func([
            $collectionClass ?: Collection\ArrayCollection::class, 'fromArray'
        ], $this->toArray());
    }

    /** {@inheritdoc} */
    public function toArray(): array {
        $values = [];
        foreach ($this as $value) {
            $values[] = $value;
        }

        return $values;
    }

    public function toStorage(): \SplObjectStorage {
        return $this->elements;
    }

    /** {@inheritdoc} */
    public function hash(): string {
        return md5(serialize($this->elements));
    }

    /** {@inheritdoc} */
    public function offsetExists($offset): bool {
        return $this->elements->offsetExists($offset);
    }

    /** {@inheritdoc} */
    public function offsetGet($offset) {
        return $this->elements->offsetGet($offset);
    }

    /** {@inheritdoc} */
    public function offsetSet($offset, $value): void {
        throw new \BadMethodCallException('Unable to change immutable map');
    }

    /** {@inheritdoc} */
    public function offsetUnset($offset): void {
        throw new \BadMethodCallException('Unable to change immutable map');
    }
}