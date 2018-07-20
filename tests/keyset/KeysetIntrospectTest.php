<?hh // strict
/*
 *  Copyright (c) 2004-present, Facebook, Inc.
 *  All rights reserved.
 *
 *  This source code is licensed under the MIT license found in the
 *  LICENSE file in the root directory of this source tree.
 *
 */

use namespace HH\Lib\Keyset;
use function Facebook\FBExpect\expect;
use type Facebook\HackTest\HackTestCase; // @oss-enable

/**
 * @emails oncall+hack
 */
final class KeysetIntrospectTest extends HackTestCase {

  public static function provideTestEqual(): varray<mixed> {
    return varray[
      tuple(
        keyset[1, 2, 3],
        keyset[1, 2, 3],
        true,
      ),
      tuple(
        keyset[1, 2, 3],
        keyset[1, 2],
        false,
      ),
      tuple(
        keyset[1, 2, 3],
        keyset[1, 2, 4],
        false,
      ),
      tuple(
        keyset[1, 2, 3],
        keyset[1, 2, 3, 4],
        false,
      ),
      tuple(
        keyset[1, 2, 3],
        keyset[1, 2, '3'],
        false,
      ),
      tuple(
        keyset[1, 2, 3],
        keyset[1, 3, 2],
        true,
      ),
    ];
  }

  /** @dataProvider provideTestEqual */
  public function testEqual<Tv as arraykey>(
    keyset<Tv> $keyset1,
    keyset<Tv> $keyset2,
    bool $expected,
  ): void {
    expect(Keyset\equal($keyset1, $keyset2))->toBeSame($expected);
  }
}
