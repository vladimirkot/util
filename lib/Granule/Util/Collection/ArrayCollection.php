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

namespace Granule\Util\Collection;

use Granule\Util\Collection;
use Granule\Util\StrictTypedValue;

class ArrayCollection implements Collection {
    protected $elements = [];

    public function __construct(CollectionBuilder $builder) {
        if ($this instanceof StrictTypedValue) {
            if ($builder instanceof StrictTypedValue) {
                if ($this->getValueType() !== $builder->getValueType()) {
                    throw new \TypeError(sprintf(
                        'Expected type StrictTypedCollectionBuilder<%s> provided: StrictTypedCollectionBuilder<%s>',
                        $this->getValueType(), $builder->getValueType()
                    ));
                }
            } else {
                throw new \TypeError(sprintf(
                    'Expected type StrictTypedCollectionBuilder<%s> provided: CollectionBuilder<...>',
                    $this->getValueType()
                ));
            }
        }

        $this->elements = $builder->getElements();
    }

    public static function builder(): CollectionBuilder {
        if (is_a(static::class,StrictTypedValue::class, true)) {
            $reflection = new \ReflectionClass(static::class);
            /** @var StrictTypedValue $fake */
            $fake = $reflection->newInstanceWithoutConstructor();

            return new StrictTypedCollectionBuilder(static::class, $fake->getValueType());
        }

        return new CollectionBuilder(static::class);
    }

    public static function fromArray(array $elements): ArrayCollection {
        $builder = static::builder();

        foreach ($elements as $element) {
            $builder->add($element);
        }

        return $builder->build();
    }

    /** {@inheritdoc} */
    public function getIterator(): \Iterator {
        return new \ArrayIterator($this->elements);
    }

    /** {@inheritdoc} */
    public function contains($element): bool {
        $this->validateElement($element);

        return in_array($element, $this->elements);
    }

    /** {@inheritdoc} */
    public function containsAll(Collection $collection): bool {
        return $collection->toArray()
            == array_uintersect($collection->toArray(), $this->elements, function ($e1, $e2) {
                if ($e1 == $e2) {
                    return 0;
                }

                return -1;
            });
    }

    /** {@inheritdoc} */
    public function equals(Collection $collection): bool {
        if ($this instanceof StrictTypedValue && $collection instanceof StrictTypedValue) {
            if ($this->getValueType() !== $collection->getValueType()) {
                return false;
            }
        }

        return $this->toArray() == $collection->toArray();
    }

    /** {@inheritdoc} */
    public function isEmpty(): bool {
        return $this->count() === 0;
    }

    /** {@inheritdoc} */
    public function count(): int {
        return count($this->elements);
    }

    /** {@inheritdoc} */
    public function toArray(): array {
        return $this->elements;
    }

    /** {@inheritdoc} */
    public function get(int $index) {
        if ($this->offsetExists($index)) {
            return $this->elements[$index];
        }

        throw new \OutOfRangeException(sprintf('Undefined element by index: %d', $index));
    }

    /** {@inheritdoc} */
    public function indexOf($element): ?int {
        $this->validateElement($element);
        $value = array_search($element, $this->elements, true);

        return $value !== false ? $value : null;
    }

    /** {@inheritdoc} */
    public function offsetExists($offset): bool {
        if (!is_integer($offset)) {
            throw new \TypeError(
                sprintf('Expected integer type, provided: %s', gettype($offset))
            );
        }

        return array_key_exists($offset, $this->elements);
    }

    /** {@inheritdoc} */
    public function offsetGet($offset) {
        return $this->get($offset);
    }

    /** {@inheritdoc} */
    public function offsetSet($offset, $value): void {
        throw new \BadMethodCallException('Unable to change immutable collection');
    }

    /** {@inheritdoc} */
    public function offsetUnset($offset): void {
        throw new \BadMethodCallException('Unable to change immutable collection');
    }

    /** {@inheritdoc} */
    public function hash(): string {
        return md5(serialize($this));
    }

    protected function validateElement($element): void {
        if (($this instanceof StrictTypedValue) && ($type = $this->getValueType())) {
            if (!is_a($element, $type)) {
                $actualType = is_object($element)
                    ? get_class($element)
                    : gettype($element);
                throw new \TypeError(
                    sprintf('Expected type %s provided: %s', $type, $actualType)
                );
            }
        }
    }
}