<?hh // strict
/*
 *  Copyright (c) 2004-present, Facebook, Inc.
 *  All rights reserved.
 *
 *  This source code is licensed under the MIT license found in the
 *  LICENSE file in the root directory of this source tree.
 *
 */

use namespace HH\Lib\{Math, Str};
use function Facebook\FBExpect\expect;
use type Facebook\HackTest\DataProvider; // @oss-enable
use type Facebook\HackTest\HackTest as HackTestCase; // @oss-enable

// @oss-disable: <<Oncalls('hack')>>
final class MathContainersTest extends HackTestCase {

  public static function provideTestMax(): varray<mixed> {
    return varray[
      tuple(
        varray[],
        null,
      ),
      tuple(
        Set {8, 6, 7, 5, 3, 0, 9},
        9,
      ),
      tuple(
        HackLibTestTraversables::getIterator(
          varray[8, 6, 7, 5, 3, 0, 9],
        ),
        9,
      ),
    ];
  }

  <<DataProvider('provideTestMax')>>
  public function testMax<T as num>(
    Traversable<T> $numbers,
    ?T $expected,
  ): void {
    expect(Math\max($numbers))->toBeSame($expected);
  }

  public static function provideTestMaxBy(): varray<mixed> {
    return varray[
      tuple(
        varray[],
        $x ==> $x,
        null,
      ),
      tuple(
        vec['the', 'quick', 'brown', 'fox'],
        $s ==> Str\length($s),
        'brown',
      ),
      tuple(
        HackLibTestTraversables::getIterator(
          varray['the', 'quick', 'brown', 'fox'],
        ),
        $s ==> Str\length($s),
        'brown',
      ),
    ];
  }

  <<DataProvider('provideTestMaxBy')>>
  public function testMaxBy<T>(
    Traversable<T> $traversable,
    (function(T): num) $num_func,
    ?T $expected,
  ): void {
    expect(Math\max_by($traversable, $num_func))->toBeSame($expected);
  }

  public static function provideTestMean(): varray<mixed> {
    return varray[
      tuple(vec[1.0, 2.0, 3, 4], 2.5),
      tuple(vec[1, 1, 2], 4 / 3),
      tuple(vec[-1, 1], 0.0),
      tuple(vec[], null),
    ];
  }

  <<DataProvider('provideTestMean')>>
  public function testMean(
    Container<num> $numbers,
    ?float $expected
  ): void {
    $actual = Math\mean($numbers);
    if ($expected === null) {
      expect($actual)->toBeNull();
    } else {
      expect($actual)->toAlmostEqual($expected);
    }
  }

  public static function provideTestMedian(): varray<mixed> {
    return varray[
      tuple(vec[], null),
      tuple(vec[1], 1.0),
      tuple(vec[1, 2], 1.5),
      tuple(vec[1, 2, 3], 2.0),
      tuple(vec[9, -1], 4.0),
      tuple(vec[200, -500, 3], 3.0),
      tuple(vec[0, 1, 0, 0], 0.0),
    ];
  }

  <<DataProvider('provideTestMedian')>>
  public function testMedian(
    Container<num> $numbers,
    ?float $expected,
  ): void {
    if ($expected === null) {
      expect(Math\median($numbers))->toBeNull();
    } else {
      expect(Math\median($numbers))->toBeSame($expected);
    }
  }

  public static function provideTestMin(): varray<mixed> {
    return varray[
      tuple(
        varray[],
        null,
      ),
      tuple(
        Set {8, 6, 7, 5, 3, 0, 9},
        0,
      ),
      tuple(
        HackLibTestTraversables::getIterator(
          varray[8, 6, 7, 5, 3, 0, 9],
        ),
        0,
      ),
      tuple(
        Vector {8, 6, 7, -5, -3, 0, 9},
        -5,
      ),
    ];
  }

  <<DataProvider('provideTestMin')>>
  public function testMin<T as num>(
    Traversable<T> $traversable,
    ?T $expected,
  ): void {
    expect(Math\min($traversable))->toBeSame($expected);
  }

  public static function provideTestMinBy(): varray<mixed> {
    return varray[
      tuple(
        varray[],
        $x ==> $x,
        null,
      ),
      tuple(
        vec['the', 'quick', 'brown', 'fox'],
        $s ==> Str\length($s),
        'fox',
      ),
      tuple(
        HackLibTestTraversables::getIterator(
          varray['the', 'quick', 'brown', 'fox'],
        ),
        $s ==> Str\length($s),
        'fox',
      ),
    ];
  }

  <<DataProvider('provideTestMinBy')>>
  public function testMinBy<T>(
    Traversable<T> $traversable,
    (function(T): num) $num_func,
    ?T $expected,
  ): void {
    expect(Math\min_by($traversable, $num_func))->toBeSame($expected);
  }

  public static function provideTestSum(): varray<mixed> {
    return varray[
      tuple(
        Vector {},
        0,
      ),
      tuple(
        varray[1, 2, 1, 1, 3],
        8,
      ),
      tuple(
        HackLibTestTraversables::getIterator(range(1, 4)),
        10,
      ),
    ];
  }

  <<DataProvider('provideTestSum')>>
  public function testSum(
    Traversable<int> $traversable,
    int $expected,
  ): void {
    expect(Math\sum($traversable))->toBeSame($expected);
  }

  public static function provideTestSumFloat(): varray<mixed> {
    return varray[
      tuple(
        Vector {},
        0.0,
      ),
      tuple(
        varray[1, 2.5, 1, 1, 3],
        8.5,
      ),
      tuple(
        HackLibTestTraversables::getIterator(range(1, 4)),
        10.0,
      ),
    ];
  }

  <<DataProvider('provideTestSumFloat')>>
  public function testSumFloat<T as num>(
    Traversable<T> $traversable,
    float $expected,
  ): void {
    expect(Math\sum_float($traversable))->toBeSame($expected);
  }
}
