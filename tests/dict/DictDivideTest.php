<?hh // strict
/*
 *  Copyright (c) 2004-present, Facebook, Inc.
 *  All rights reserved.
 *
 *  This source code is licensed under the MIT license found in the
 *  LICENSE file in the root directory of this source tree.
 *
 */

use namespace HH\Lib\{Dict, Vec};
use function Facebook\FBExpect\expect;
use type Facebook\HackTest\DataProvider; // @oss-enable
use type Facebook\HackTest\HackTest as HackTestCase; // @oss-enable

// @oss-disable: <<Oncalls('hack')>>
final class DictDivideTest extends HackTestCase {

  public static function providePartition(): varray<mixed> {
    return varray[
      tuple(
        Dict\associate(Vec\range(1, 10), Vec\range(11, 20)),
        $val ==> $val % 2 === 0,
        tuple(
          dict[
            2 => 12,
            4 => 14,
            6 => 16,
            8 => 18,
            10 => 20,
          ],
          dict[
            1 => 11,
            3 => 13,
            5 => 15,
            7 => 17,
            9 => 19,
          ],
        ),
      ),
      tuple(
        HackLibTestTraversables::getKeyedIterator(Dict\associate(Vec\range(1, 10), Vec\range(11, 20))),
        $val ==> $val % 2 === 0,
        tuple(
          dict[
            2 => 12,
            4 => 14,
            6 => 16,
            8 => 18,
            10 => 20,
          ],
          dict[
            1 => 11,
            3 => 13,
            5 => 15,
            7 => 17,
            9 => 19,
          ],
        ),
      ),
    ];
  }

  public static function providePartitionWithKey(): varray<mixed> {
    return varray[
      tuple(
        Dict\associate(Vec\range(1, 10), Vec\range(11, 20)),
        ($key, $val) ==> $val >= 19 || $key <= 3,
        tuple(
          dict[
            1 => 11,
            2 => 12,
            3 => 13,
            9 => 19,
            10 => 20,
          ],
          dict[
            4 => 14,
            5 => 15,
            6 => 16,
            7 => 17,
            8 => 18,
          ],
        ),
      ),
      tuple(
        HackLibTestTraversables::getKeyedIterator(Dict\associate(Vec\range(1, 10), Vec\range(11, 20))),
        ($key, $val) ==> $val >= 19 || $key <= 3,
        tuple(
          dict[
            1 => 11,
            2 => 12,
            3 => 13,
            9 => 19,
            10 => 20,
          ],
          dict[
            4 => 14,
            5 => 15,
            6 => 16,
            7 => 17,
            8 => 18,
          ],
        ),
      ),
    ];
  }

  <<DataProvider('providePartition')>>
  public function testPartition<Tk as arraykey, Tv>(
    KeyedTraversable<Tk, Tv> $traversable,
    (function(Tv): bool) $predicate,
    (dict<Tk, Tv>, dict<Tk, Tv>) $expected,
  ): void {
    expect(Dict\partition($traversable, $predicate))->toBeSame($expected);
  }

  <<DataProvider('providePartitionWithKey')>>
  public function testPartitionWithKey<Tk as arraykey, Tv>(
    KeyedTraversable<Tk, Tv> $traversable,
    (function(Tk, Tv): bool) $predicate,
    (dict<Tk, Tv>, dict<Tk, Tv>) $expected,
  ): void {
    expect(Dict\partition_with_key($traversable, $predicate))->toBeSame($expected);
  }
}
