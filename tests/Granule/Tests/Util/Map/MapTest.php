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

namespace Granule\Tests\Util\Map;

use Granule\Tests\Util\Map\_fixtures\DateToDateMap;
use Granule\Tests\Util\Map\_fixtures\DateMap;
use Granule\Util\Map;
use Granule\Util\Map\MapBuilder;
use PHPUnit\Framework\TestCase;

/**
 * @group unit
 * @coversDefaultClass \Granule\Util\ObjectMap
 */
class MapTest extends TestCase {
    public function iterableDataProvider(): array {
        $objKeys = $this->getObjectKeys();
        $strKeys = $this->getBasicKeys();
        $values = $this->getValues();

        return [
            [
                $this->getTestMap(DateToDateMap::class, $objKeys, $values),
                $objKeys,
                $values
            ],
            [
                $this->getTestMap(DateMap::class, $strKeys, $values),
                $strKeys,
                $values
            ]
        ];
    }

    /**
     * @covers ::key
     * @covers ::next
     * @covers ::valid
     * @covers ::current
     * @covers ::rewind
     * @test
     * @dataProvider iterableDataProvider
     *
     * @param Map $map
     * @param array $keys
     * @param array $values
     */
    public function it_should_be_iterable(Map $map, array $keys, array $values): void {
        $i = 0;
        foreach ($map as $key => $value) {
            $this->assertEquals($keys[$i], $key, "Invalid key on index: $i");
            $this->assertEquals($values[$i], $value, "Invalid value on index: $i");
            $i++;
        }
    }

    public function containsKeyDataBuilder(): array {
        $objKeys = $this->getObjectKeys();
        $strKeys = $this->getBasicKeys();
        $values = $this->getValues();
        $map1 = $this->getTestMap(DateToDateMap::class, $objKeys, $values);
        $map2 = $this->getTestMap(DateMap::class, $strKeys, $values);

        return [
            [$map2, $strKeys[1], true],
            [$map2, 'not_exists', false],
            [$map1, $objKeys[2], true],
            [$map1, new \DateTimeImmutable('01-01-2012'), false]
        ];
    }

    /**
     * @covers ::containsKey
     * @test
     * @dataProvider containsKeyDataBuilder
     *
     * @param Map $map
     * @param mixed $key
     * @param bool $exists
     */
    public function it_should_be_able_to_check_key_presence(Map $map, $key, bool $exists): void {
        $this->assertTrue($exists === $map->containsKey($key));
    }

    public function containsValueDataBuilder(): array {
        $objKeys = $this->getObjectKeys();
        $strKeys = $this->getBasicKeys();
        $values = $this->getValues();
        $map1 = $this->getTestMap(DateToDateMap::class, $objKeys, $values);
        $map2 = $this->getTestMap(DateMap::class, $strKeys, $values);

        return [
            [$map2, $values[2], true],
            [$map2, new \DateTimeImmutable('01-01-2012'), false],
            [$map1, $values[0], true],
            [$map1, new \DateTimeImmutable('01-01-2012'), false]
        ];
    }

    /**
     * @covers ::containsValue
     * @test
     * @dataProvider containsValueDataBuilder
     *
     * @param Map $map
     * @param \DateTimeImmutable $value
     * @param bool $exists
     */
    public function it_should_be_able_to_check_value_presence(Map $map, \DateTimeImmutable $value, bool $exists): void {
        $this->assertTrue($exists === $map->containsValue($value));
    }

    public function equalsDataBuilder(): array {
        $objectKeys = $this->getObjectKeys();
        $basicKeys = $this->getBasicKeys();
        $values = $this->getValues();

        $builder = DateToDateMap::builder();
        foreach (range(0, count($objectKeys)-1) as $index) {
            $builder->add($values[$index], $objectKeys[$index]);
        }
        $map1 = $builder->build();

        $builder = DateToDateMap::builder();
        foreach (range(0, count($objectKeys)-1) as $index) {
            $builder->add($objectKeys[$index], $values[$index]);
        }
        $map2 = $builder->build();

        $builder = DateToDateMap::builder();
        foreach (range(0, count($objectKeys)-1) as $index) {
            $builder->add($objectKeys[$index], $values[$index]);
        }
        $map3 = $builder->build();

        $builder = DateMap::builder();
        foreach (range(0, count($basicKeys)-1) as $index) {
            $builder->add($basicKeys[$index], $values[$index]);
        }
        $map4 = $builder->build();

        return [
            [$map1, $map2, false],
            [$map1, $map3, false],
            [$map3, $map2, true],
            [$map1, $map4, false],
            [$map4, $map2, false]
        ];
    }

    /**
     * @covers ::equals
     * @test
     * @dataProvider equalsDataBuilder
     *
     * @param Map $m1
     * @param Map $m2
     * @param bool $equals
     */
    public function it_should_be_able_to_check_equals(Map $m1, Map $m2, bool $equals): void {
        $this->assertTrue($equals === $m1->equals($m2));
        $this->assertTrue($m2->equals($m1) === $m1->equals($m2));
    }

    public function filterMethodDataProvider(): array {
        $ts = (new \DateTime('01-01-2012'))->getTimestamp();
        $filterData = [];
        $data = $this->iterableDataProvider();

        foreach ($data as $row) {
            $filterData[] = [
                $row[0],
                function (\DateTimeImmutable $v, $k) use ($ts) {
                    return $v->getTimestamp() < $ts;
                },
                1
            ];
            $filterData[] =[
                $row[0],
                function (\DateTimeImmutable $v, $k) use ($ts) {
                    return $v->getTimestamp() > $ts;
                },
                2
            ];
        }

        return $filterData;
    }

    /**
     * @covers ::filter
     * @test
     * @dataProvider filterMethodDataProvider
     *
     * @param Map $map
     * @param \Closure $filer
     * @param int $count
     */
    public function it_should_be_able_to_be_filtered(Map $map, \Closure $filer, int $count): void {
        $this->assertEquals($count, $map->filter($filer)->count());
    }

    public function getMethodDataProvider(): array {
        $data = [];
        foreach ($this->iterableDataProvider() as $case) {
            //          map     value           key     +-
            $data[] = [$case[0], $case[2][2], $case[1][2], true];
            $data[] = [$case[0], $case[2][0], $case[1][0], true];
        }

        return $data;
    }

    /**
     * @covers ::get
     * @test
     * @dataProvider getMethodDataProvider
     *
     * @param Map $map
     * @param mixed $key
     * @param \DateTimeInterface $value
     * @param bool $success
     */
    public function it_should_be_able_to_get_value(
        Map $map, \DateTimeInterface $value, $key, bool $success
    ): void {
        $this->assertEquals($success, $value === $map->get($key));
    }

    public function getOrDefaultMethodDataProvider(): array {
        list($map, $keys, $values) = $this->iterableDataProvider()[0];
        $defaultValue = new \DateTimeImmutable('01-01-2002');
        $wrongKey = new \DateTimeImmutable('01-01-2003');

        return [
            [$map, $values[0], $keys[0], $defaultValue],
            [$map, $values[2], $keys[2], $defaultValue],
            [$map, $defaultValue, $wrongKey, $defaultValue]
        ];
    }

    /**
     * @covers ::getOrDefault
     * @test
     * @dataProvider getOrDefaultMethodDataProvider
     *
     * @param Map $map
     * @param \DateTimeInterface $value
     * @param \DateTimeInterface $key
     * @param \DateTimeInterface $default
     */
    public function it_should_be_able_to_get_value_with_default(
        Map $map,
        \DateTimeInterface $value,
        \DateTimeInterface $key,
        \DateTimeInterface $default
    ): void {
        $this->assertEquals($value, $map->getOrDefault($key, $default));
    }

    public function isEmptyMethodDataProvider(): array {
        list($map, $keys, $_) = $this->iterableDataProvider()[0];

        return [
            [$map, count($keys) === 0],
            [DateToDateMap::builder()->build(), true]
        ];
    }

    /**
     * @covers ::isEmpty
     * @test
     * @dataProvider isEmptyMethodDataProvider
     *
     * @param Map $map
     * @param bool $empty
     */
    public function it_should_be_able_to_check_emptiness(Map $map, bool $empty): void {
        $this->assertTrue($empty === $map->isEmpty());
    }

    /**
     * @covers ::keys
     * @test
     * @dataProvider iterableDataProvider
     *
     * @param Map $map
     * @param \DateTimeImmutable[] $keys
     * @param \DateTimeImmutable[] $values
     */
    public function it_should_be_able_to_check_keys(Map $map, array $keys, array $values): void {
        $this->assertEquals($keys, $map->keys());
    }

    public function countableDataProvider(): array {
        $src = $this->iterableDataProvider();
        $data = [];

        $data[] = [
            DateToDateMap::builder()->build(),
            0
        ];

        foreach ($src as $row) {
            $data[] = [
                $row[0], // Map
                count($row[1])
            ];
        }

        return $data;
    }

    /**
     * @covers ::count
     * @test
     * @dataProvider countableDataProvider
     *
     * @param Map $map
     * @param int $count
     */
    public function it_should_be_countable(Map $map, int $count): void {
        $this->assertEquals($count, $map->count());
    }

    /**
     * @covers ::values
     * @test
     * @dataProvider iterableDataProvider
     *
     * @param Map $map
     * @param \DateTimeImmutable[] $keys
     * @param \DateTimeImmutable[] $values
     */
    public function it_should_be_able_to_check_values(Map $map, array $keys, array $values): void {
        $this->assertEquals($values, $map->values()->toArray());
    }

    /**
     * @covers ::toArray
     * @test
     * @dataProvider iterableDataProvider
     *
     * @param Map $map
     * @param \DateTimeImmutable[] $keys
     * @param \DateTimeImmutable[] $values
     */
    public function it_should_be_able_to_convert_to_array(Map $map, array $keys, array $values): void {
        $this->assertEquals($values, array_values($map->toArray()));
    }

    private function getTestMap(string $class, array $keys, array $values): Map {
        /** @var MapBuilder $builder */
        $builder = $class::builder();
        foreach (range(0, count($keys)-1) as $index) {
            $builder->add($keys[$index], $values[$index]);
        }

        return $builder->build();
    }

    private function getObjectKeys(): array {
        return [
            new \DateTimeImmutable('01-01-2001'),
            new \DateTimeImmutable('02-01-2010'),
            new \DateTimeImmutable('03-01-2090')
        ];
    }

    private function getBasicKeys(): array {
        return ['one', 'two', 'three'];
    }

    private function getValues(): array {
        return [
            new \DateTimeImmutable('10-10-2010'),
            new \DateTimeImmutable('11-10-2020'),
            new \DateTimeImmutable('12-10-2030')
        ];
    }
}