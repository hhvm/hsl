<?hh // strict
/*
 *  Copyright (c) 2004-present, Facebook, Inc.
 *  All rights reserved.
 *
 *  This source code is licensed under the MIT license found in the
 *  LICENSE file in the root directory of this source tree.
 *
 */

use namespace HH\Lib\C;
use function Facebook\FBExpect\expect;
use type Facebook\HackTest\DataProvider; // @oss-enable
use type Facebook\HackTest\HackTest as HackTestCase; // @oss-enable

// @oss-disable: <<Oncalls('hack')>>
final class CIntrospectTest extends HackTestCase {

  public static function provideTestAny(): varray<mixed> {
    return varray[
      tuple(
        Vector {2, 4, 6, 8, 9, 10, 12},
        $v ==> $v % 2 === 1,
        true,
      ),
      tuple(
        Vector {2, 4, 6, 8, 10, 12},
        $v ==> $v % 2 === 1,
        false,
      ),
    ];
  }

  <<DataProvider('provideTestAny')>>
  public function testAny<T>(
    Traversable<T> $traversable,
    (function(T): bool) $predicate,
    bool $expected,
  ): void {
    expect(C\any($traversable, $predicate))->toBeSame($expected);
  }

  public static function provideTestAnyWithoutPredicate(): varray<mixed> {
    return varray[
      tuple(
        varray[],
        false,
      ),
      tuple(
        varray[null, 0, '0', ''],
        false,
      ),
      tuple(
        HackLibTestTraversables::getIterator(
          varray[null, 0, '0', '', 1],
        ),
        true,
      ),
    ];
  }

  <<DataProvider('provideTestAnyWithoutPredicate')>>
  public function testAnyWithoutPredicate<T>(
    Traversable<T> $traversable,
    bool $expected,
  ): void {
    expect(C\any($traversable))->toBeSame($expected);
  }

  public static function provideTestContains(): varray<mixed> {
    return varray[
      tuple(
        vec[1, 2, 3, 4, 5],
        3,
        true,
      ),
      tuple(
        vec[1, 2, '3', 4, 5],
        3,
        false,
      ),
      tuple(
        keyset[1, 2, 3, 4, 5],
        3,
        true,
      ),
      tuple(
        keyset[1, 2, '3', 4, 5],
        3,
        false,
      ),
      tuple(
        HackLibTestTraversables::getIterator(range(1, 5)),
        3,
        true,
      ),
      tuple(
        varray[dict[1 => 2, 3 => 4]],
        dict[1 => 2, 3 => 4],
        true,
      ),
      tuple(
        varray[varray[3]],
        varray[4],
        false,
      ),
    ];
  }

  <<DataProvider('provideTestContains')>>
  public function testContains<T>(
    Traversable<T> $traversable,
    T $value,
    bool $expected,
  ): void {
    expect(C\contains($traversable, $value))->toBeSame($expected);
  }

  public static function provideTestContainsKey(): varray<mixed> {
    return varray[
      tuple(
        darray[3 => 3],
        3,
        true,
      ),
      tuple(
        dict['3' => 3],
        3,
        false,
      ),
      tuple(
        dict[],
        3,
        false,
      ),
      tuple(
        Map {'foo' => 'bar'},
        'bar',
        false,
      ),
      tuple(
        Vector {'the', 'quick', 'brown', 'fox'},
        4,
        false,
      ),
      tuple(
        Vector {'the', 'quick', 'brown', 'fox'},
        0,
        true,
      ),
    ];
  }

  <<DataProvider('provideTestContainsKey')>>
  public function testContainsKey<Tk, Tv>(
    KeyedContainer<Tk, Tv> $container,
    Tk $key,
    bool $expected,
  ): void {
    expect(C\contains_key($container, $key))->toBeSame($expected);
  }

  public static function provideTestCount(): varray<mixed> {
    return varray[
      tuple(varray[], 0),
      tuple(range(1, 10), 10),
      tuple(Set {1, 2}, 2),
      tuple(Vector {1, 2}, 2),
      tuple(Map {'foo' => 'bar', 'baz' => 'bar2'}, 2),
      tuple(keyset[1, 2, 3], 3),
      tuple(vec[1, 2, 3], 3),
      tuple(dict['foo' => 'bar', 'baz' => 'bar2'], 2),
    ];
  }

  <<DataProvider('provideTestCount')>>
  public function testCount<T>(
    Container<T> $container,
    int $expected,
  ): void {
    expect(C\count($container))->toBeSame($expected);
  }

  public static function provideTestEvery(): varray<mixed> {
    return varray[
      tuple(
        Vector {2, 4, 6, 8, 9, 10, 12},
        $v ==> $v % 2 === 0,
        false,
      ),
      tuple(
        Vector {2, 4, 6, 8, 10, 12},
        $v ==> $v % 2 === 0,
        true,
      ),
    ];
  }

  <<DataProvider('provideTestEvery')>>
  public function testEvery<T>(
    Traversable<T> $traversable,
    (function(T): bool) $predicate,
    bool $expected,
  ): void {
    expect(C\every($traversable, $predicate))->toBeSame($expected);
  }

  public static function provideTestEveryWithoutPredicate(): varray<mixed> {
    return varray[
      tuple(
        varray[],
        true,
      ),
      tuple(
        HackLibTestTraversables::getIterator(range(1, 5)),
        true,
      ),
    ];
  }

  <<DataProvider('provideTestEveryWithoutPredicate')>>
  public function testEveryWithoutPredicate<T>(
    Traversable<T> $traversable,
    bool $expected,
  ): void {
    expect(C\every($traversable))->toBeSame($expected);
  }

  public static function provideTestIsEmpty(): varray<mixed> {
    return varray[
      tuple(varray[], true),
      tuple(varray[1], false),
      tuple(darray['foo' => 'bar'], false),
      tuple(dict[], true),
      tuple(dict['foo' => 'bar'], false),
      tuple(vec[], true),
      tuple(vec[1], false),
      tuple(keyset[], true),
      tuple(keyset[1], false),
      tuple(Map {}, true),
      tuple(Map {'foo' => 'bar'}, false),
      tuple(Vector {}, true),
      tuple(Vector {1}, false),
      tuple(Set {}, true),
      tuple(Set {1}, false),
    ];
  }

  <<DataProvider('provideTestIsEmpty')>>
  public function testIsEmpty<T>(
    Container<T> $container,
    bool $expected,
  ): void {
    expect(C\is_empty($container))->toBeSame($expected);
  }
}
