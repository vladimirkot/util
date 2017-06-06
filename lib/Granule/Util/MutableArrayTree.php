<?php

namespace Granule\Util;

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
        $this->recursiveSearch($this->data, array_shift($offset), [], $offset,
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