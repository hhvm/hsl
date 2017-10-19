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
final class MathCompareTest extends PHPUnit_Framework_TestCase {

  public static function provideTestMaxv(): array<mixed> {
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

  /** @dataProvider provideTestMaxv */
  public function testMaxv<T as num>(
    T $first,
    Container<T> $rest,
    T $expected,
  ): void {
    expect(Math\maxv($first, ...$rest))->toBeSame($expected);
  }

  public static function provideTestMinv(): array<mixed> {
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

  /** @dataProvider provideTestMinv */
  public function testMinv<T as num>(
    T $first,
    Container<T> $rest,
    T $expected,
  ): void {
    expect(Math\minv($first, ...$rest))->toBeSame($expected);
  }
}
