<?hh // strict
/*
 *  Copyright (c) 2004-present, Facebook, Inc.
 *  All rights reserved.
 *
 *  This source code is licensed under the MIT license found in the
 *  LICENSE file in the root directory of this source tree.
 *
 */

use namespace HH\Lib\Vec;
use function Facebook\FBExpect\expect; // @oss-enable
use type Facebook\HackTest\{DataProvider, HackTest}; // @oss-enable

// @oss-disable: <<Oncalls('hack')>>
final class VecCombineTest extends HackTest {

  public static function provideTestConcat(): varray<mixed> {
    return varray[
      tuple(
        varray[],
        varray[],
        vec[],
      ),
      tuple(
        vec[],
        varray[
          darray[], Vector {}, Map {}, Set {},
        ],
        vec[],
      ),
      tuple(
        varray['the', 'quick'],
        varray[
          Vector {'brown', 'fox'},
          Map {'jumped' => 'over'},
          HackLibTestTraversables::getIterator(varray['the', 'lazy', 'dog']),
        ],
        vec['the', 'quick', 'brown', 'fox', 'over', 'the', 'lazy', 'dog'],
      ),
    ];
  }

  <<DataProvider('provideTestConcat')>>
  public function testConcat<Tv>(
    Traversable<Tv> $first,
    Container<Container<Tv>> $rest,
    vec<Tv> $expected,
  ): void {
    expect(Vec\concat($first, ...$rest))->toBeSame($expected);
  }

}
