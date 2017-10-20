<?hh // strict
/*
 *  Copyright (c) 2004-present, Facebook, Inc.
 *  All rights reserved.
 *
 *  This source code is licensed under the BSD-style license found in the
 *  LICENSE file in the root directory of this source tree. An additional grant
 *  of patent rights can be found in the PATENTS file in the same directory.
 *
 */

use namespace HH\Lib\Math;
use function Facebook\FBExpect\expect;

/**
 * @emails oncall+hack_prod_infra
 */
final class MathContainersTest extends PHPUnit_Framework_TestCase {

  public static function provideTestMaxBy(): array<mixed> {
    return array(
      tuple(
        array(),
        $x ==> $x,
        null,
      ),
      tuple(
        vec['the', 'quick', 'brown', 'fox'],
        fun('strlen'),
        'brown',
      ),
      tuple(
        HackLibTestTraversables::getIterator(
          array('the', 'quick', 'brown', 'fox'),
        ),
        fun('strlen'),
        'brown',
      ),
    );
  }

  /** @dataProvider provideTestMaxBy */
  public function testMaxBy<T>(
    Traversable<T> $traversable,
    (function(T): num) $num_func,
    ?T $expected,
  ): void {
    expect(Math\max_by($traversable, $num_func))->toBeSame($expected);
  }

  public static function provideTestMean(): array<mixed> {
    return array(
      tuple(vec[1.0, 2.0, 3, 4], 2.5),
      tuple(vec[1, 1, 2], 4 / 3),
      tuple(vec[-1, 1], 0.0),
      tuple(vec[], null),
    );
  }

  /** @dataProvider provideTestMean */
  public function testMean(
    Container<num> $numbers,
    ?float $expected
  ): void {
    $actual = Math\mean($numbers);
    if ($expected === null) {
      expect($actual)->toBeSame(null);
    } else {
      expect($actual)->toAlmostEqual($expected);
    }
  }

  public static function provideTestMedian(): array<mixed> {
    return array(
      tuple(vec[], null),
      tuple(vec[1], 1.0),
      tuple(vec[1, 2], 1.5),
      tuple(vec[1, 2, 3], 2.0),
      tuple(vec[9, -1], 4.0),
      tuple(vec[200, -500, 3], 3.0),
      tuple(vec[0, 1, 0, 0], 0.0),
    );
  }

  /** @dataProvider provideTestMedian */
  public function testMedian(
    Container<num> $numbers,
    ?float $expected,
  ): void {
    if ($expected === null) {
      expect(Math\median($numbers))->toBeSame(null);
    } else {
      expect(Math\median($numbers))->toBeSame($expected);
    }
  }

  public static function provideTestMinBy(): array<mixed> {
    return array(
      tuple(
        array(),
        $x ==> $x,
        null,
      ),
      tuple(
        vec['the', 'quick', 'brown', 'fox'],
        fun('strlen'),
        'fox',
      ),
      tuple(
        HackLibTestTraversables::getIterator(
          array('the', 'quick', 'brown', 'fox'),
        ),
        fun('strlen'),
        'fox',
      ),
    );
  }

  /** @dataProvider provideTestMinBy */
  public function testMinBy<T>(
    Traversable<T> $traversable,
    (function(T): num) $num_func,
    ?T $expected,
  ): void {
    expect(Math\min_by($traversable, $num_func))->toBeSame($expected);
  }

  public static function provideTestSum(): array<mixed> {
    return array(
      tuple(
        Vector {},
        0,
      ),
      tuple(
        array(1, 2, 1, 1, 3),
        8,
      ),
      tuple(
        HackLibTestTraversables::getIterator(range(1, 4)),
        10,
      ),
    );
  }

  /** @dataProvider provideTestSum */
  public function testSum(
    Traversable<int> $traversable,
    int $expected,
  ): void {
    expect(Math\sum($traversable))->toBeSame($expected);
  }

  public static function provideTestSumFloat(): array<mixed> {
    return array(
      tuple(
        Vector {},
        0.0,
      ),
      tuple(
        array(1, 2.5, 1, 1, 3),
        8.5,
      ),
      tuple(
        HackLibTestTraversables::getIterator(range(1, 4)),
        10.0,
      ),
    );
  }

  /** @dataProvider provideTestSumFloat */
  public function testSumFloat<T as num>(
    Traversable<T> $traversable,
    float $expected,
  ): void {
    expect(Math\sum_float($traversable))->toBeSame($expected);
  }
}
