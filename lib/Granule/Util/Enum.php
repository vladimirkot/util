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

/**
 *  Simple enumeration implementation
 *
 * Usage:
 * <code>
 *  /**
 *   * Class Month
 *   * @ method static Month January()
 *   * @ method static Month February()
 *   *\/
 * class Month extends \Granule\Component\Enum\Enum {
 *   const
 *       January = 1,
 *       February = 2;
 *      ...
 * }
 *
 * $m1 = Month::January();
 * $m2 = Month::January();
 *
 * $m1->equalTo($m2); // -> true
 * </code>
 */
abstract class Enum {
    /** @var string */
    private $value;
    /** @var array Enum[] */
    private static $pool = [];

    private final function __construct(string $value, array $arguments = null) {
        $this->value = $value;

        if (method_exists($this, '__init')) {
            call_user_func_array([$this, '__init'], $arguments);
        }
    }

    private final function __clone() {
    }

    public final function getValue(): string {
        return $this->value;
    }

    public final function equalTo(Enum $another): bool {
        return static::class === get_class($another)
                    && $this->getValue() === $another->getValue();
    }

    public static final function __callStatic(string $name, $_): Enum {
        $arguments = self::getConstantArguments($name);
        if (!$arguments) {
            throw new \BadMethodCallException(sprintf('Type %s doesn\'t contain value %s', static::class, $name));
        }
        if (!array_key_exists(static::class, self::$pool)) {
            self::$pool[static::class] = [];
        }
        if (!array_key_exists($name, self::$pool[static::class])) {
            if (!array_key_exists($name, self::$pool[static::class])) {
                self::$pool[static::class][$name] = new static($name, $arguments);
            }
        }

        return self::$pool[static::class][$name];
    }

    /**
     * @param string $value
     * @return static
     */
    public static function fromValue(string $value): Enum {
        return self::__callStatic($value, []);
    }

    public static final function hasValue(string $value): bool {
        return array_key_exists($value, self::getValues());
    }

    public static final function getValues(): array {
        $reflectionClass = new \ReflectionClass(static::class);
//        if (!$reflectionClass->isFinal()) {
//            throw new \ParseError(static::class);
//        }

        return $reflectionClass->getConstants();
    }

    private static function getConstantArguments(string $value): ?array {
        $values = self::getValues();

        if (!array_key_exists($value, $values)) {
            return null;
        }

        $data = $values[$value];

        return is_array($data) ? $data : [$data];
    }

    public function __toString(): string {
        return $this->getValue();
    }
}