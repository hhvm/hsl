<?hh // strict
/*
 *  Copyright (c) 2004-present, Facebook, Inc.
 *  All rights reserved.
 *
 *  This source code is licensed under the MIT license found in the
 *  LICENSE file in the root directory of this source tree.
 *
 */

use namespace HH\Lib\Dict;
use function Facebook\FBExpect\expect;
use type Facebook\HackTest\DataProvider; // @oss-enable
use type Facebook\HackTest\HackTest as HackTestCase; // @oss-enable

// @oss-disable: <<Oncalls('hack')>>
final class DictIntrospectTest extends HackTestCase {

  public static function provideTestEqual(): varray<mixed> {
    return varray[
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
    ];
  }

  <<DataProvider('provideTestEqual')>>
  public function testEqual<Tk as arraykey, Tv>(
    dict<Tk, Tv> $dict1,
    dict<Tk, Tv> $dict2,
    bool $expected,
  ): void {
    expect(Dict\equal($dict1, $dict2))->toBeSame($expected);
  }
}
