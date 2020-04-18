<?hh // strict
/*
 *  Copyright (c) 2004-present, Facebook, Inc.
 *  All rights reserved.
 *
 *  This source code is licensed under the MIT license found in the
 *  LICENSE file in the root directory of this source tree.
 *
 */

use namespace HH\Lib\Math;
use function Facebook\FBExpect\expect; // @oss-enable
use type Facebook\HackTest\{DataProvider, HackTest}; // @oss-enable

// @oss-disable: <<Oncalls('hack')>>
final class MathCompareTest extends HackTest {

  public static function provideTestMaxva(): vec<(num, num, Container<num>, num)> {
    return vec[
      tuple(1, 2, vec[], 2),
      tuple(2, 1, vec[], 2),
      tuple(1.0, 2.0, vec[], 2.0),
      tuple(2.0, 1.0, vec[], 2.0),
      tuple(1, 1, vec[], 1),
      tuple(-2, -1, vec[], -1),
      tuple(1.0, 2, vec[], 2),
      tuple(1, 2.0, vec[], 2.0),
      tuple(-1, 1, vec[2, 3, 4, 5], 5),
      tuple(-1, 5, vec[4, 3, 2, 1], 5),
    ];
  }

  <<DataProvider('provideTestMaxva')>>
  public function testMaxva<T as num>(
    T $first,
    T $second,
    Container<T> $rest,
    T $expected,
  ): void {
    expect(Math\maxva($first, $second, ...$rest))->toEqual($expected);
  }

  public static function provideTestMinva(): vec<(num, num, Container<num>, num)> {
    return vec[
      tuple(1, 2, vec[], 1),
      tuple(2, 1, vec[], 1),
      tuple(1.0, 2.0, vec[], 1.0),
      tuple(2.0, 1.0, vec[], 1.0),
      tuple(1, 1, vec[], 1),
      tuple(-2, -1, vec[], -2),
      tuple(1.0, 2, vec[], 1.0),
      tuple(1, 2.0, vec[], 1),
      tuple(1, -1, vec[-2, -3, -4, -5], -5),
      tuple(1, -5, vec[-4, -3, -2, -1], -5),
    ];
  }

  <<DataProvider('provideTestMinva')>>
  public function testMinva<T as num>(
    T $first,
    T $second,
    Container<T> $rest,
    T $expected,
  ): void {
    expect(Math\minva($first, $second, ...$rest))->toEqual($expected);
  }

  public static function provideTestIsNan(): vec<(num, bool)> {
    return vec[
      tuple(0, false),
      tuple(1, false),
      tuple(-1, false),
      tuple(0.0, false),
      tuple(0.1, false),
      tuple(-0.1, false),
      tuple(Math\NAN, true),
    ];
  }

  <<DataProvider('provideTestIsNan')>>
  public function testIsNan(num $number, bool $is_nan): void {
    expect(Math\is_nan($number))->toEqual($is_nan);
  }
}
