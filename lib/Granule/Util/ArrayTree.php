<?php

namespace Granule\Util;

class ArrayTree implements Tree {
    /** @var array */
    protected $data;
    /** @var array */
    protected $srcPath = [];

    protected function __construct(array &$data, array $srcPath = []) {
        $this->data = &$data;
        $this->srcPath = $srcPath;
    }

    public static function fromArray(array $data, array $srcPath = []): ArrayTree {
        return new static($data, $srcPath);
    }

    public static function fromArrayByReference(array &$data, array $srcPath = []): ArrayTree {
        return new static($data, $srcPath);
    }

    public function key() {
        return key($this->data);
    }

    public function next(): void {
        next($this->data);
    }

    public function valid(): bool {
        return key($this->data) !== null;
    }

    public function current() {
        $element = &$this->data[key($this->data)];
        return is_array($element)
            ? static::fromArrayByReference($element, $this->srcPath)
            : $element;
    }

    public function rewind(): void {
        reset($this->data);
    }

    public function count(): int {
        return count($this->data);
    }

    public function serialize(): string {
        return serialize($this->data);
    }

    public function unserialize($serialized): ArrayTree {
        return unserialize($serialized);
    }

    public function jsonSerialize(): array {
        return $this->toArray();
    }

    public function offsetSet($offset, $value): void {
        throw new \BadMethodCallException('Immutable structure: You are not allowed to change data');
    }

    public function offsetUnset($offset) {
        throw new \BadMethodCallException('Immutable structure: You are not allowed to remove data');
    }

    public function offsetExists($offset): bool {
        $offset = $this->extractKey($offset);
        return $this->recursiveSearch($this->data, array_shift($offset), [], $offset, function () {
            return true;
        }, function () {
            return false;
        });
    }

    public function offsetGet($offset) {
        $offset = $this->extractKey($offset);
        return $this->recursiveSearch($this->data, array_shift($offset), [], $offset, function (&$value, array $path) {
            return is_array($value) ? static::fromArrayByReference($value, $path) : $value;
        }, function (array $path) {
            throw new \OutOfBoundsException(sprintf('Element "%s" not found', implode('.', $path)));
        });
    }

    public function __invoke(string $offset, $default = null) {
        $offset = $this->extractKey($offset);
        return $this->recursiveSearch($this->data, array_shift($offset), [], $offset, function (&$value, array $path) {
            return is_array($value) ? static::fromArrayByReference($value, $path) : $value;
        }, function () use ($default) {
            return $default;
        });
    }

    public function toMutable(): MutableArrayTree {
        return MutableArrayTree::fromArrayByReference($this->data, $this->srcPath);
    }

    public function toImmutable(): ArrayTree {
        return $this;
    }

    public function toArray(): array {
        return $this->data;
    }

    public function hash(): string {
        return md5(serialize($this->data));
    }

    protected function recursiveSearch(
        array &$data, string $key, array $path, array $next,
        \Closure $onFound = null, \Closure $onNotFound = null
    ) {
        $path[] = $key;
        if (array_key_exists($key, $data)) {
            if (is_array($data[$key])) {
                if (!$next) {
                    return $onFound($data[$key], $path, $data, $key); // the value
                } else {
                    return $this->recursiveSearch($data[$key], array_shift($next), $path, $next, $onFound, $onNotFound); // next step
                }
            }

            return $onFound($data[$key], $path, $data, $key); // also the value
        }

        return $onNotFound(array_merge($this->srcPath, $path));
    }

    protected function extractKey(string $key): array {
        $key = str_replace('\.', '{{DOT}}', $key, $count);
        $key = explode('.', $key);

        if($count) {
            foreach ($key as $index => $item) {
                $key[$index] = str_replace('{{DOT}}', '.', $item);
            }
        }

        return $key;
    }
}