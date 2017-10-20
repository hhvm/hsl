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
  public static function provideTestMax(): array<mixed> {
    return array(
      tuple(1, vec[2], 2),
      tuple(2, vec[1], 2),
      tuple(1.0, vec[2.0], 2.0),
      tuple(2.0, vec[1.0], 2.0),
      tuple(1, vec[1], 1),
      tuple(-2, vec[-1], -1),
      tuple(1.0, vec[2], 2),
      tuple(1, vec[2.0], 2.0),
      tuple(-1, vec[1, 2, 3, 4, 5], 5),
      tuple(-1, vec[5, 4, 3, 2, 1], 5),
    );
  }

  /** @dataProvider provideTestMax */
  public function testMax<T as num>(
    T $first_number,
    Container<T> $numbers,
    T $expected,
  ): void {
    expect(Math\maxv($first_number, ...$numbers))->toBeSame($expected);
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

  public static function provideTestMin(): array<mixed> {
    return array(
      tuple(1, vec[2], 1),
      tuple(2, vec[1], 1),
      tuple(1.0, vec[2.0], 1.0),
      tuple(2.0, vec[1.0], 1.0),
      tuple(1, vec[1], 1),
      tuple(-2, vec[-1], -2),
      tuple(1.0, vec[2], 1.0),
      tuple(1, vec[2.0], 1),
      tuple(1, vec[-1, -2, -3, -4, -5], -5),
      tuple(1, vec[-5, -4, -3, -2, -1], -5),
    );
  }

  /** @dataProvider provideTestMin */
  public function testMin<T as num>(
    T $first_number,
    Container<T> $numbers,
    T $expected,
  ): void {
    expect(Math\min($first_number, ...$numbers))->toBeSame($expected);
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
