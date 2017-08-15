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
final class KeysetOrderTest extends PHPUnit_Framework_TestCase {

  public static function provideSort(): array<mixed> {
    return array(
      tuple(
        vec['the', 'quick', 'brown', 'fox'],
        null,
        keyset['brown', 'fox', 'quick', 'the'],
      ),
      tuple(
        Vector {'the', 'quick', 'brown', 'fox'},
        ($a, $b) ==> strcmp($a[1],$b[1]),
        keyset['the', 'fox', 'brown', 'quick'],
      ),
      tuple(
        array(8, 6, 7, 5, 3, 0, 9),
        null,
        keyset[0, 3, 5, 6, 7, 8, 9],
      ),
      tuple(
        HackLibTestTraversables::getIterator(array(8, 6, 7, 5, 3, 0, 9)),
        null,
        keyset[0, 3, 5, 6, 7, 8, 9],
      ),
    );
  }

  /** @dataProvider provideSort */
  public function testSort<Tv as arraykey>(
    Traversable<Tv> $traversable,
    ?(function(Tv, Tv): int) $comparator,
    keyset<Tv> $expected,
  ): void {
    expect(KeysetHSL\sort($traversable, $comparator))->toBeSame($expected);
  }

}
