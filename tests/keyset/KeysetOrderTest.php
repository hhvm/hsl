<?hh // strict
/*
 *  Copyright (c) 2004-present, Facebook, Inc.
 *  All rights reserved.
 *
 *  This source code is licensed under the MIT license found in the
 *  LICENSE file in the root directory of this source tree.
 *
 */

use namespace HH\Lib\{Keyset, Str};
use function Facebook\FBExpect\expect; // @oss-enable
use type Facebook\HackTest\{DataProvider, HackTest}; // @oss-enable

// @oss-disable: <<Oncalls('hack')>>
final class KeysetOrderTest extends HackTest {

  public static function provideSort(): vec<(Traversable<mixed>, ?(function(nothing, nothing): int), keyset<arraykey>)> {
    return vec[
      tuple(
        vec['the', 'quick', 'brown', 'fox'],
        null,
        keyset['brown', 'fox', 'quick', 'the'],
      ),
      tuple(
        Vector {'the', 'quick', 'brown', 'fox'},
        /* HH_FIXME[4297] The type of the lambda argument(s) could not be inferred */
        ($a, $b) ==> Str\compare($a[1],$b[1]),
        keyset['the', 'fox', 'brown', 'quick'],
      ),
      tuple(
        varray[8, 6, 7, 5, 3, 0, 9],
        null,
        keyset[0, 3, 5, 6, 7, 8, 9],
      ),
      tuple(
        HackLibTestTraversables::getIterator(varray[8, 6, 7, 5, 3, 0, 9]),
        null,
        keyset[0, 3, 5, 6, 7, 8, 9],
      ),
    ];
  }

  <<DataProvider('provideSort')>>
  public function testSort<Tv as arraykey>(
    Traversable<Tv> $traversable,
    ?(function(Tv, Tv): int) $comparator,
    keyset<Tv> $expected,
  ): void {
    expect(Keyset\sort($traversable, $comparator))->toEqual($expected);
  }

}
