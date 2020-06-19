<?hh // strict
/*
 *  Copyright (c) 2004-present, Facebook, Inc.
 *  All rights reserved.
 *
 *  This source code is licensed under the MIT license found in the
 *  LICENSE file in the root directory of this source tree.
 *
 */

use namespace HH\Lib\{Keyset, Vec};
use function Facebook\FBExpect\expect; // @oss-enable
use type Facebook\HackTest\{DataProvider, HackTest}; // @oss-enable

// @oss-disable: <<Oncalls('hack')>>
final class KeysetDivideTest extends HackTest {

  public static function providePartition(): vec<(Traversable<mixed>, (function(nothing): bool), (keyset<arraykey>, keyset<arraykey>))> {
    return vec[
      tuple(
        Vec\range(1, 10),
        $n ==> $n % 2 === 0,
        tuple(
          keyset[2, 4, 6, 8, 10],
          keyset[1, 3, 5, 7, 9],
        ),
      ),
      tuple(
        HackLibTestTraversables::getIterator(Vec\range(1, 10)),
        $n ==> $n % 2 === 0,
        tuple(
          keyset[2, 4, 6, 8, 10],
          keyset[1, 3, 5, 7, 9],
        ),
      ),
    ];
  }

  <<DataProvider('providePartition')>>
  public function testPartition<Tv as arraykey>(
    Traversable<Tv> $traversable,
    (function(Tv): bool) $predicate,
    (keyset<Tv>, keyset<Tv>) $expected,
  ): void {
    expect(Keyset\partition($traversable, $predicate))->toEqual($expected);
  }
}
