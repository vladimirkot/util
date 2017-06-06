<?php

namespace Granule\Tests\Util;

use Granule\Util\Enum;
use Granule\Tests\Util\TO\{Month, Planet};
use PHPUnit\Framework\TestCase;

/**
 * @group core
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