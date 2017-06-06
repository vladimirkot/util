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

namespace Granule\Tests\Util\TO;

use Granule\Util\Enum;

/**
 * @method static Planet MERCURY()
 * @method static Planet VENUS()
 * @method static Planet EARTH()
 * @method static Planet MARS()
 * @method static Planet JUPITER()
 * @method static Planet SATURN()
 * @method static Planet URANUS()
 * @method static Planet NEPTUNE()
 */
class Planet extends Enum {
    private const
        MERCURY = [3.303e+23, 2.4397e6],
        VENUS = [4.869e+24, 6.0518e6],
        EARTH = [5.976e+24, 6.37814e6],
        MARS = [6.421e+23, 3.3972e6],
        JUPITER = [1.9e+27,   7.1492e7],
        SATURN = [5.688e+26, 6.0268e7],
        URANUS = [8.686e+25, 2.5559e7],
        NEPTUNE = [1.024e+26, 2.4746e7];

    /** @var float */
    private $mass;   // in kilograms
    /** @var float */
    private $radius; // in meters

    public function __init(float $mass, float $radius) {
        $this->mass = $mass;
        $this->radius = $radius;
    }

    public function getMass(): float {
        return $this->mass;
    }

    public function getRadius(): float {
        return $this->radius;
    }

    // universal gravitational constant  (m3 kg-1 s-2)
    public static $G = 6.67300E-11;

    public function surfaceGravity(): float {
        return self::$G * $this->mass / ($this->radius * $this->radius);
    }

    public function surfaceWeight(float $otherMass): float {
        return $otherMass * $this->surfaceGravity();
    }
}