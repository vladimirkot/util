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

namespace Granule\Tests\Util\Tree;

use Granule\Util\Tree\{ArrayTree, MutableArrayTree};
use PHPUnit\Framework\TestCase;

/**
 * @group unit
 * @group tree
 * @coversDefaultClass Granule\Util\Tree\ArrayTree
 * @coversDefaultClass Granule\Util\Tree\MutableArrayTree
 */
class ArrayTreeTest extends TestCase {
    /** @var array */
    private static $data = [
        'var 0' => 'string var 0',
        'var 1' => [
            ['var 1.0.0', 'var 1.0.1', 'var 1.0.2'],
            ['var 1.1.0', 'var 1.1.1', 'var 1.1.2'],
            ['var 1.2.0', 'var 1.2.1']
        ],
        'var 2' => 2,
        'var 3' => false,
        'var 4' => '',
        'var 5' => [
            'var 5.0' => 'value string 5.0',
            'var 5.1' => 5.1,
            'var 5.2' => true
        ]
    ];
    /** @var MutableArrayTree */
    private static $mutableTree;
    /** @var ArrayTree */
    private static $immutableTree;

    public static function setUpBeforeClass(): void {
        self::$mutableTree = MutableArrayTree::fromArray(self::$data);
        self::$immutableTree = ArrayTree::fromArray(self::$data);
    }

    /**
     * @test
     * @covers ::offsetGet
     */
    public function data_should_be_accessible_by_key(): void {
        $tree = self::$immutableTree;

        $this->assertEquals('string var 0', $tree['var 0']);
        $this->assertEquals(2, $tree['var 2']);
        $this->assertEquals(false, $tree['var 3']);
        $this->assertInstanceOf(ArrayTree::class, $tree['var 1']);
        $this->assertEquals('var 1.0.1', self::$data['var 1'][0][1]);
        $this->assertFalse(isset($tree['var 7']));
        $this->assertFalse(array_key_exists('var 7', $tree));
        $this->assertFalse(isset($tree['var 1'][3][1]));
        $this->assertFalse(array_key_exists('var 1.0.5', $tree));
    }

    /**
     * @test
     * @covers ::offsetGet
     */
    public function data_should_be_accessible_by_path(): void {
        $this->assertEquals('var 1.1.1', self::$mutableTree['var 1.1.1'], 'Numeric path elements failure');
        $this->assertEquals('var 1.0.1', self::$mutableTree['var 1.0.1'], 'Nullable numeric path elements failure');
        $this->assertEquals('value string 5.0', self::$mutableTree['var 5.var 5\.0'], 'Character escaping failure');
    }

    /**
     * @test
     * @covers ::current
     * @covers ::next
     * @covers ::key
     * @covers ::valid
     * @covers ::rewind
     */
    public function it_should_be_iterable(): void {
        $index = 0;
        foreach (self::$mutableTree as $key => $value) {
            $this->assertEquals("var {$index}", $key);
            $this->assertEquals(is_array(self::$data[$key])
                    ? MutableArrayTree::fromArray(self::$data[$key], [$key])
                    : self::$data[$key],
                $value, sprintf('Key is %s', $key));

            $index++;
        }
    }

    /**
     * @test
     * @covers ::count
     */
    public function it_should_be_countable(): void {
        $this->assertEquals(6, count(self::$mutableTree));
        $this->assertEquals(3, count(self::$mutableTree['var 1']));
        $this->assertEquals(3, count(self::$mutableTree['var 1.0']));
    }

    /**
     * @test
     * @covers ::toImmutable
     * @covers ::toMutable
     */
    public function it_should_be_convertible_to_mutable_and_back(): void {
        $this->assertEquals(self::$immutableTree, self::$immutableTree->toImmutable());
        $this->assertEquals(self::$immutableTree, self::$mutableTree->toImmutable());
        $this->assertEquals(self::$mutableTree, self::$mutableTree->toMutable());
        $this->assertEquals(self::$mutableTree, self::$immutableTree->toMutable());
    }

    /**
     * @test
     * @covers ::toArray
     */
    public function it_should_be_convertible_to_array(): void {
        $this->assertEquals(self::$data, self::$immutableTree->toArray());
        $this->assertEquals(self::$data, self::$mutableTree->toArray());
    }

    /**
     * @test
     * @covers ::offsetSet
     * @covers ::offsetUnset
     */
    public function mutable_tree_should_be_mutable(): void {
        $mutableTree = MutableArrayTree::fromArray(self::$data);
        $mutableTree['var 0'] = 'string 000';
        $mutableTree['var 1'][0][1] = 'string 1.0.1 but other one';

        $this->assertEquals('string 000', $mutableTree['var 0']);
        $this->assertEquals('string 1.0.1 but other one', $mutableTree['var 1'][0][1]);
        $this->assertEquals('string 1.0.1 but other one', $mutableTree['var 1.0.1']);

        unset($mutableTree['var 1'][0][1]);
        unset($mutableTree['var 1.1.1']);

        $this->assertFalse(isset($mutableTree['var 1'][0][1]));
        $this->assertFalse(isset($mutableTree['var 1.0.1']));
        $this->assertFalse(isset($mutableTree['var 1.1.1']));
    }

    /**
     * @test
     * @expectedException \OutOfBoundsException
     * @expectedExceptionMessage Element "var 1.3" not found
     */
    public function it_should_throw_exception_when_xpath_not_found(): void {
        self::$mutableTree['var 1.3'];
    }

    /**
     * @test
     * @expectedException \OutOfBoundsException
     * @expectedExceptionMessage Element "var 1.3" not found
     */
    public function it_should_throw_exception_when_key_not_found(): void {
        self::$mutableTree['var 1'][3];
    }

    /**
     * @test
     * @expectedException \OutOfBoundsException
     * @expectedExceptionMessage Element "var 1.2.2" not found
     */
    public function it_should_throw_exception_when_index_key_not_found(): void {
        foreach (self::$mutableTree['var 1'] as $index => $value) {
            $value[2];
        }
    }

    /**
     * @test
     * @expectedException \OutOfBoundsException
     * @expectedExceptionMessage Element "var 1.0.5" not found
     */
    public function it_should_throw_exception_when_0_key_not_found(): void {
        foreach (self::$immutableTree['var 1'] as $index => $value) {
            $value[5];
        }
    }
}