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

use \HH\Lib\Dict as DictHSL;
use function \Facebook\FBExpect\expect;

/**
 * @emails oncall+hack_prod_infra
 */
final class DictIntrospectTest extends PHPUnit_Framework_TestCase {

  public static function provideTestEqual(): array<mixed> {
    return array(
      tuple(
        dict[1 => 1, 2 => 2, 3 => 3],
        dict[1 => 1, 2 => 2, 3 => 3],
        true,
      ),
      tuple(
        dict[1 => 1, 2 => 2, 3 => 3],
        dict[1 => 1, 2 => 2],
        false,
      ),
      tuple(
        dict[1 => 1, 2 => 2, 3 => 3],
        dict[1 => 1, 2 => 2, 4 => 4],
        false,
      ),
      tuple(
        dict[1 => 1, 2 => 2, 3 => 3],
        dict[1 => 1, 2 => 2, '3' => 3],
        false,
      ),
      tuple(
        dict[1 => 1, 2 => 2, 3 => 3],
        dict[1 => 1, 2 => 2, 3 => '3'],
        false,
      ),
      tuple(
        dict[1 => 1, 2 => 2, 3 => 3],
        dict[1 => 1, 3 => 3, 2 => 2],
        true,
      ),
    );
  }

  /** @dataProvider provideTestEqual */
  public function testEqual<Tk, Tv>(
    dict<Tk, Tv> $dict1,
    dict<Tk, Tv> $dict2,
    bool $expected,
  ): void {
    expect(DictHSL\equal($dict1, $dict2))->toBeSame($expected);
  }
}
