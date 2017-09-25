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

use namespace HH\Lib\Keyset;
use function Facebook\FBExpect\expect;

/**
 * @emails oncall+hack_prod_infra
 */
final class KeysetDivideTest extends PHPUnit_Framework_TestCase {

  public static function providePartition(): array<mixed> {
    return array(
      tuple(
        range(1, 10),
        $n ==> $n % 2 === 0,
        tuple(
          keyset[2, 4, 6, 8, 10],
          keyset[1, 3, 5, 7, 9],
        ),
      ),
      tuple(
        HackLibTestTraversables::getIterator(range(1, 10)),
        $n ==> $n % 2 === 0,
        tuple(
          keyset[2, 4, 6, 8, 10],
          keyset[1, 3, 5, 7, 9],
        ),
      ),
    );
  }

  /** @dataProvider providePartition */
  public function testPartition<Tv as arraykey>(
    Traversable<Tv> $traversable,
    (function(Tv): bool) $predicate,
    (keyset<Tv>, keyset<Tv>) $expected,
  ): void {
    expect(Keyset\partition($traversable, $predicate))->toBeSame($expected);
  }
}
