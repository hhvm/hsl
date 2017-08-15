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

use \HH\Lib\Keyset as KeysetHSL;
use function \Facebook\FBExpect\expect;

/**
 * @emails oncall+hack_prod_infra
 */
final class KeysetIntrospectTest extends PHPUnit_Framework_TestCase {

  public static function provideTestEqual(): array<mixed> {
    return array(
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
    );
  }

  /** @dataProvider provideTestEqual */
  public function testEqual<Tv as arraykey>(
    keyset<Tv> $keyset1,
    keyset<Tv> $keyset2,
    bool $expected,
  ): void {
    expect(KeysetHSL\equal($keyset1, $keyset2))->toBeSame($expected);
  }
}
