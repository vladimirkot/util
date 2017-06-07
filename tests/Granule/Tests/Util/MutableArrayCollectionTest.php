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

use Granule\Tests\Util\TO\MutableDateCollection;
use Granule\Util\ArrayCollection;
use Granule\Util\MutableCollection;
use PHPUnit\Framework\TestCase;

/**
 * @qroup unit
 * @coversDefaultClass Granule\Util\MutableArrayCollection
 */
class MutableArrayCollectionTest extends TestCase {
    public function provider(): array {
        return [
            [
                [
                    'first' => new \DateTime('14-01-2014'),
                    'second' => new \DateTime('12-11-2011'),
                    'third' => new \DateTime('17-12-2017')
                ],
                MutableDateCollection::class
            ]
        ];
    }

    /**
     * @covers ::add
     * @test
     * @dataProvider provider
     *
     * @param array $fixture
     * @param string $class
     */
    public function it_should_be_able_to_add_element(array $fixture, string $class): void {
        /** @var MutableCollection $collection */
        $collection = $class::fromArray($fixture);
        $newElement = new \DateTime('10-12-2017');

        $this->assertEquals(3, $collection->count());

        $collection->add($newElement);

        $this->assertEquals(4, $collection->count());
        $this->assertTrue($collection->contains($newElement));
    }

    /**
     * @covers ::add
     * @test
     * @dataProvider provider
     * @expectedException \TypeError
     * @expectedExceptionMessage Expected type DateTime provided: DateTimeImmutable
     *
     * @param array $fixture
     * @param string $class
     */
    public function it_should_not_be_able_to_add_element_with_wrong_type(array $fixture, string $class): void {
        /** @var MutableCollection $collection */
        $collection = $class::fromArray($fixture);
        $newElement = new \DateTimeImmutable('10-12-2017');
        $collection->add($newElement);
    }

    /**
     * @covers ::addAll
     * @test
     * @dataProvider provider
     *
     * @param array $fixture
     * @param string $class
     */
    public function it_should_be_able_to_add_collection_of_elements(array $fixture, string $class): void {
        /** @var MutableCollection $collection */
        $collection = $class::fromArray($fixture);
        $newElements = ArrayCollection::fromArray([
            new \DateTime('10-12-2017'),
            new \DateTime('11-12-2017')
        ]);

        $this->assertEquals(3, $collection->count());

        $collection->addAll($newElements);

        $this->assertEquals(5, $collection->count());
        $this->assertTrue($collection->contains($newElements[0]));
        $this->assertTrue($collection->contains($newElements[1]));
    }

    /**
     * @covers ::addAll
     * @test
     * @dataProvider provider
     * @expectedException \TypeError
     * @expectedExceptionMessage Expected type DateTime provided: DateTimeImmutable
     *
     * @param array $fixture
     * @param string $class
     */
    public function it_should_not_be_able_to_add_collection_with_wrong_element_type(
        array $fixture, string $class
    ): void {
        /** @var MutableCollection $collection */
        $collection = $class::fromArray($fixture);
        $newElements = ArrayCollection::fromArray([
            new \DateTime('11-12-2017'),
            new \DateTimeImmutable('11-12-2017'),
        ]);

        $collection->addAll($newElements);
    }

    /**
     * @covers ::clear
     * @test
     * @dataProvider provider
     *
     * @param array $fixture
     * @param string $class
     */
    public function it_should_be_able_to_clear_collection(array $fixture, string $class): void {
        /** @var MutableCollection $collection */
        $collection = $class::fromArray($fixture);

        $this->assertEquals(3, $collection->count());

        $collection->clear();

        $this->assertTrue(0 === $collection->count());
    }

    /**
     * @covers ::remove
     * @test
     * @dataProvider provider
     *
     * @param array $fixture
     * @param string $class
     */
    public function it_should_be_able_to_remove_element(array $fixture, string $class): void {
        /** @var MutableCollection $collection */
        $collection = $class::fromArray($fixture);
        $values = array_values($fixture);
        $wrong = new \DateTime('11-12-2017');

        $this->assertEquals(3, $collection->count());
        $this->assertTrue($collection->contains($values[1]));

        $this->assertTrue($collection->remove($values[1]));

        $this->assertEquals(2, $collection->count());
        $this->assertFalse($collection->contains($values[1]));

        $this->assertFalse($collection->contains($wrong));
        $this->assertFalse($collection->remove($wrong));

        $this->assertEquals(2, $collection->count());
        $this->assertFalse($collection->contains($wrong));
    }

    /**
     * @covers ::remove
     * @test
     * @dataProvider provider
     * @expectedException \TypeError
     * @expectedExceptionMessage Expected type DateTime provided: DateTimeImmutable
     *
     * @param array $fixture
     * @param string $class
     */
    public function it_should_throw_exception_on_removing_element_having_wrong_type(
        array $fixture, string $class
    ): void {
        /** @var MutableCollection $collection */
        $collection = $class::fromArray($fixture);
        $wrong = new \DateTimeImmutable('11-12-2017');

        $collection->remove($wrong);
    }

    /**
     * @covers ::removeAll
     * @test
     * @dataProvider provider
     *
     * @param array $fixture
     * @param string $class
     */
    public function it_should_be_able_to_remove_all_elements(array $fixture, string $class): void {
        /** @var MutableCollection $collection */
        $collection = $class::fromArray($fixture);
        /** @var MutableCollection $collection */
        $collection2 = ArrayCollection::fromArray($fixture);
        $values = array_values($fixture);

        $this->assertEquals(3, $collection->count());
        $this->assertTrue($collection->removeAll($collection2));
        $this->assertEquals(0, $collection->count());
        $this->assertFalse($collection->contains($values[0]));
        $this->assertFalse($collection->contains($values[1]));
        $this->assertFalse($collection->contains($values[2]));
    }

    /**
     * @covers ::removeIf
     * @test
     * @dataProvider provider
     *
     * @param array $fixture
     * @param string $class
     */
    public function it_should_be_able_to_remove_elements_by_condition(array $fixture, string $class): void {
        /** @var MutableCollection $collection */
        $collection = $class::fromArray($fixture);
        $values = array_values($fixture);
        $timestamp = (new \DateTime('12-12-2012'))->getTimestamp();

        $this->assertEquals(3, $collection->count());
        $this->assertTrue($collection->removeIf(function (\DateTime $datetime, int $index) use ($timestamp) {
            return $datetime->getTimestamp() > $timestamp;
        }));

        $this->assertEquals(1, $collection->count());
        $this->assertFalse($collection->contains($values[0]));
        $this->assertTrue($collection->contains($values[1]));
        $this->assertFalse($collection->contains($values[2]));
    }

    /**
     * @covers ::retainAll
     * @test
     * @dataProvider provider
     *
     * @param array $fixture
     * @param string $class
     */
    public function it_should_be_able_to_reatain_elements(array $fixture, string $class): void {
        /** @var MutableCollection $collection */
        $collection = $class::fromArray($fixture);
        $values = array_values($fixture);
        $toRetain = $class::fromArray([
            $values[1],
            new \DateTime('12-12-2012')
        ]);

        $this->assertEquals(3, $collection->count());
        $this->assertTrue($collection->retainAll($toRetain));

        $this->assertEquals(1, $collection->count());
        $this->assertFalse($collection->contains($values[0]));
        $this->assertTrue($collection->contains($values[1]));
        $this->assertFalse($collection->contains($values[2]));
    }
}