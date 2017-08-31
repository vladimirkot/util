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

namespace Granule\Tests\Util\Collection;

use Granule\Tests\Util\Collection\_fixtures\DateCollection;
use Granule\Util\Collection;
use PHPUnit\Framework\TestCase;

/**
 * @group unit
 * @group collection
 * @coversDefaultClass Granule\Util\Collection\ArrayCollection
 */
class ArrayCollectionTest extends TestCase {
    public function provider(): array {
        return [
            [
                [
                    'first' => new \DateTimeImmutable('14-01-2014'),
                    'second' => new \DateTimeImmutable('12-11-2011'),
                    'third' => new \DateTimeImmutable('17-12-2017')
                ],
                DateCollection::class
            ]
        ];
    }

    /**
     * @covers ::contains
     * @test
     * @dataProvider provider
     *
     * @param array $fixture
     * @param string $class
     */
    public function it_should_contain_specific_element(array $fixture, string $class): void {
        /** @var Collection $collection */
        $collection = Collection\ArrayCollection::fromArray($fixture);

        $this->assertTrue($collection->contains(end($fixture)));
        $this->assertFalse($collection->contains(new \DateTimeImmutable('now')));
        $this->assertTrue($collection->contains(new \DateTime('17-12-2017')));
    }

    /**
     * @covers ::contains
     * @test
     * @dataProvider provider
     * @expectedException \TypeError
     * @expectedExceptionMessage Expected type DateTimeImmutable provided: DateTime
     *
     * @param array $fixture
     * @param string $class
     */
    public function on_strict_mode_contains_method_should_throw_exception(array $fixture, string $class): void {
        /** @var Collection $collection */
        $collection = $class::fromArray($fixture);
        $collection->contains(new \DateTime('17-12-2017'));
    }

    /**
     * @covers ::containsAll
     * @test
     * @dataProvider provider
     *
     * @param array $fixture
     * @param string $class
     */
    public function it_should_contain_specific_collection_of_elements(array $fixture, string $class): void {
        /** @var Collection $collection */
        $collection = Collection\ArrayCollection::fromArray($fixture);
        /** @var Collection $other */
        $other = $class::fromArray($fixture);

        $fixture[] = new \DateTimeImmutable('07-07-2017');
        /** @var Collection $other2 */
        $other2 = $class::fromArray($fixture);

        $this->assertTrue($collection->containsAll($other));
        $this->assertTrue($other->containsAll($collection));
        $this->assertFalse($collection->containsAll($other2));
        $this->assertTrue($other2->containsAll($other));
    }

    /**
     * @covers ::equals
     * @covers ::toArray
     * @test
     * @dataProvider provider
     *
     * @param array $fixture
     * @param string $class
     */
    public function it_should_be_able_to_check_equals(array $fixture, string $class): void {
        /** @var Collection $other */
        $collection = Collection\ArrayCollection::fromArray($fixture);
        /** @var Collection $other */
        $other = $class::fromArray($fixture);

        $this->assertTrue($collection->equals($other));
        $this->assertTrue($other->equals($collection));

        $fixture[] = new \DateTimeImmutable('07-07-2017');
        /** @var Collection $other2 */
        $other2 = $class::fromArray($fixture);

        $this->assertFalse($other->equals($other2));
        $this->assertFalse($other2->equals($other));
        $this->assertFalse($collection->equals($other2));
        $this->assertFalse($other2->equals($collection));
    }

    /**
     * @covers ::isEmpty
     * @test
     * @dataProvider provider
     *
     * @param array $fixture
     * @param string $class
     */
    public function it_should_be_able_to_check_emptiness(array $fixture, string $class): void {
        /** @var Collection $collection */
        $collection = Collection\ArrayCollection::fromArray($fixture);
        /** @var Collection $collection */
        $emptyCollection = Collection\ArrayCollection::fromArray([]);

        $this->assertFalse($collection->isEmpty());
        $this->assertTrue($emptyCollection->isEmpty());
    }

    /**
     * @covers ::count
     * @test
     * @dataProvider provider
     *
     * @param array $fixture
     * @param string $class
     */
    public function it_should_be_countable(array $fixture, string $class): void {
        /** @var Collection $collection */
        $collection = Collection\ArrayCollection::fromArray($fixture);

        $this->assertEquals(3, count($collection));
        $this->assertEquals(3, $collection->count());

        /** @var Collection $collection */
        $collection = $class::fromArray($fixture);

        $this->assertEquals(3, count($collection));
        $this->assertEquals(3, $collection->count());
    }

    /**
     * @covers ::get
     * @test
     * @dataProvider provider
     *
     * @param array $fixture
     * @param string $class
     */
    public function it_should_be_readable(array $fixture, string $class): void {
        /** @var Collection $collection */
        $collection = $class::fromArray($fixture);

        $this->assertEquals(new \DateTime('14-01-2014'), $collection->get(0));
        $this->assertEquals(new \DateTime('17-12-2017'), $collection->get(2));
    }

    /**
     * @covers ::get
     * @test
     * @dataProvider provider
     * @expectedException \OutOfRangeException
     * @expectedExceptionMessage Undefined element by index: 10
     *
     * @param array $fixture
     * @param string $class
     */
    public function it_should_throw_exception_when_try_to_read_nonexistent_element(array $fixture, string $class): void {
        /** @var Collection $collection */
        $collection = $class::fromArray($fixture);

        $collection->get(10);
    }

    /**
     * @covers ::indexOf
     * @test
     * @dataProvider provider
     *
     * @param array $fixture
     * @param string $class
     */
    public function it_should_be_able_to_get_element_index(array $fixture, string $class): void {
        /** @var Collection $collection */
        $collection = $class::fromArray($fixture);
        $values = array_values($fixture);

        $this->assertTrue(null === $collection->indexOf(new \DateTimeImmutable('14-01-2014')));
        $this->assertTrue(null === $collection->indexOf(new \DateTimeImmutable('12-11-2011')));
        $this->assertTrue(1 == $collection->indexOf($values[1]));
        $this->assertTrue(2 == $collection->indexOf($values[2]));
    }

    /**
     * @covers ::getIterator
     * @test
     * @dataProvider provider
     *
     * @param array $fixture
     * @param string $class
     */
    public function it_should_be_iterable(array $fixture, string $class): void {
        /** @var Collection $collection */
        $collection = $class::fromArray($fixture);
        $values = array_values($fixture);

        $i = 0;
        foreach ($collection as $index => $element) {
            $this->assertEquals($i, $index);
            $this->assertEquals($values[$i], $element);
            $i++;
        }
    }

    /**
     * @covers ::offsetGet
     * @covers ::offsetExists
     * @test
     * @dataProvider provider
     *
     * @param array $fixture
     * @param string $class
     */
    public function it_should_be_array_accessible(array $fixture, string $class): void {
        /** @var Collection $collection */
        $collection = $class::fromArray($fixture);
        $values = array_values($fixture);

        $this->assertEquals($values[1], $collection[1]);
        $this->assertEquals($values[0], $collection[0]);
        $this->assertEquals($values[2], $collection[2]);
        $this->assertTrue(isset($collection[2]));
        $this->assertFalse(isset($collection[3]));
        $this->assertFalse(empty($collection[2]));
    }

    /**
     * @covers ::hash
     * @test
     * @dataProvider provider
     *
     * @param array $fixture
     * @param string $class
     */
    public function it_should_be_hashable(array $fixture, string $class): void {
        /** @var Collection $collection */
        $collection = $class::fromArray($fixture);

        $this->assertEquals('32da861b22c9671caaf91597324f5c93', $collection->hash());
    }
}