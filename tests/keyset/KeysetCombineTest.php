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
use function Facebook\FBExpect\expect; // @oss-enable
use type Facebook\HackTest\{DataProvider, HackTest}; // @oss-enable

// @oss-disable: <<Oncalls('hack')>>
final class KeysetCombineTest extends HackTest {

  public static function provideTestUnion(): vec<(Traversable<arraykey>, Traversable<Container<arraykey>>, keyset<arraykey>)> {
    return vec[
      tuple(
        varray[],
        varray[
          vec[],
        ],
        keyset[],
      ),
      tuple(
        keyset[1, 2, 3],
        varray[
          Vector {'the', 'quick', 'brown'},
          darray[
            'the' => 'the',
            'quick' => 'quick',
            'brown' => 'brown',
            'fox' => 'jumped',
          ],
        ],
        keyset[1, 2, 3, 'the', 'quick', 'brown', 'jumped'],
      ),
    ];
  }

  <<DataProvider('provideTestUnion')>>
  public function testUnion<Tv as arraykey>(
    Traversable<Tv> $first,
    Traversable<Container<Tv>> $rest,
    keyset<Tv> $expected,
  ): void {
    expect(Keyset\union($first, ...$rest))->toEqual($expected);
  }
}
