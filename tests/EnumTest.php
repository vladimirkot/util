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

namespace Granule\Tests\Util;

use Granule\Util\Enum;
use Granule\Tests\Util\_fixtures\{Month, Planet};
use PHPUnit\Framework\TestCase;

/**
 * @group unit
 * @group enum
 * @coversDefaultClass \Granule\Util\Enum
 */
class EnumTest extends TestCase {

    /**
     * @test
     * @covers ::__callStatic
     * @covers ::__construct
     */
    public function should_be_able_to_get_enum_instance() {
        /** @var Planet $earth */
        $earth = Planet::EARTH();

        $this->assertInstanceOf(Enum::class, $earth,
            "Returned type on instantiation is incorrect");
        $this->assertEquals($earth, Planet::EARTH(),
            "Instantiation shouldn't create object every time");
        $this->assertNotEquals($earth, Planet::MARS());
    }

    /**
     * @test
     * @covers ::__init
     * @covers ::__construct
     */
    public function should_be_able_to_pass_arguments_to_init_enum() {
        $this->assertEquals( 5.976e+24, Planet::EARTH()->getMass());
        $this->assertEquals( 6.37814e6, Planet::EARTH()->getRadius());
        $this->assertEquals( 1, Month::January()->getPosition());
        $this->assertEquals( 2, Month::February()->getPosition());
    }

    /**
     * @test
     * @covers ::getValue
     */
    public function should_return_correct_enum_value() {
        $this->assertEquals('January', Month::January()->getValue());
        $this->assertEquals('EARTH', Planet::EARTH()->getValue());
    }

    /**
     * @test
     * @expectedException \Error
     * @covers ::__clone
     */
    public function should_not_allow_cloning() {
        clone Month::January();
    }

    /**
     * @test
     * @covers ::equalTo
     */
    public function should_be_able_to_check_equality() {
        $this->assertTrue(Month::January()->equalTo(Month::January()));
        $this->assertTrue(Month::January()->equalTo(Month::January()));
        $this->assertFalse(Month::January()->equalTo(Planet::EARTH()));
        $this->assertFalse(Month::January()->equalTo(Month::February()));
        $this->assertTrue(Month::January() === Month::January());
    }

    /**
     * @test
     * @covers ::hasValue
     */
    public function should_let_check_if_value_exists() {
        $this->assertTrue(Month::hasValue('January'));
        $this->assertFalse(Month::hasValue('EARTH'));
    }
}