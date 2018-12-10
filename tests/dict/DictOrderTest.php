<?hh // strict
/*
 *  Copyright (c) 2004-present, Facebook, Inc.
 *  All rights reserved.
 *
 *  This source code is licensed under the MIT license found in the
 *  LICENSE file in the root directory of this source tree.
 *
 */

use namespace HH\Lib\{Dict, Str};
use function Facebook\FBExpect\expect;
use type Facebook\HackTest\{DataProvider, HackTest}; // @oss-enable

// @oss-disable: <<Oncalls('hack')>>
final class DictOrderTest extends HackTest {

  public static function provideTestReverse(): varray<mixed> {
    return varray[
      tuple(
        Map {},
        dict[],
      ),
      tuple(
        darray[
          'the' => 'quick',
          'brown' => 'fox',
          'jumped' => 'over',
          'a' => 'dog',
        ],
        dict[
          'a' => 'dog',
          'jumped' => 'over',
          'brown' => 'fox',
          'the' => 'quick',
        ],
      ),
      tuple(
        /* HH_IGNORE_ERROR[2049] __PHPStdLib */
        /* HH_IGNORE_ERROR[4107] __PHPStdLib */
        HackLibTestTraversables::getIterator(range(1, 5)),
        dict[
          4 => 5,
          3 => 4,
          2 => 3,
          1 => 2,
          0 => 1,
        ],
      ),
    ];
  }

  <<DataProvider('provideTestReverse')>>
  public function testReverse<Tk as arraykey, Tv>(
    KeyedTraversable<Tk, Tv> $traversable,
    dict<Tk, Tv> $expected,
  ): void {
    expect(Dict\reverse($traversable))->toBeSame($expected);
  }

  public static function provideTestSort(): varray<mixed> {
    return varray[
      tuple(
        Map {
          '0' => 'the',
          '1' => 'quick',
          '2' => 'brown',
          '3' => 'fox',
        },
        null,
        dict[
          '2' => 'brown',
          '3' => 'fox',
          '1' => 'quick',
          '0' => 'the',
        ],
      ),
      tuple(
        Map {
          'a' => 'the',
          'b' => 'quick',
          'c' => 'brown',
          'd' => 'fox',
        },
        null,
        dict[
          'c' => 'brown',
          'd' => 'fox',
          'b' => 'quick',
          'a' => 'the',
        ],
      ),
      tuple(
        darray[
          0 => 'the',
          1 => 'quick',
          2 => 'brown',
          3 => 'fox',
        ],
        ($a, $b) ==> $a[1] <=> $b[1],
        dict[
          0 => 'the',
          3 => 'fox',
          2 => 'brown',
          1 => 'quick',
        ],
      ),
      tuple(
        HackLibTestTraversables::getKeyedIterator(varray[
          'the', 'quick', 'brown', 'fox',
        ]),
        ($a, $b) ==> $b[1] <=> $a[1],
        dict[
          1 => 'quick',
          2 => 'brown',
          3 => 'fox',
          0 => 'the',
        ],
      ),
    ];
  }


  <<DataProvider('provideTestSort')>>
  public function testSort<Tk as arraykey, Tv>(
    KeyedTraversable<Tk, Tv> $traversable,
    ?(function(Tv, Tv): int) $value_comparator,
    dict<Tk, Tv> $expected,
  ): void {
    expect(Dict\sort($traversable, $value_comparator))->toBeSame($expected);
  }

  public static function provideTestSortBy(): varray<mixed> {
    return varray[
      tuple(
        varray['the', 'quick', 'brown', 'fox', 'jumped'],
        $s ==> Str\reverse($s),
        null,
        dict[
          4 => 'jumped',
          0 => 'the',
          1 => 'quick',
          2 => 'brown',
          3 => 'fox',
        ],
      ),
      tuple(
        Map {
          0 => varray['eddard', 'stark'],
          1 => varray['arya', 'stark'],
          2 => varray['tyrion', 'lannister'],
          3 => varray['joffrey', 'boratheon'],
          4 => varray['daenerys', 'targaryen'],
        },
        fun('HH\\Lib\\Vec\\reverse'),
        ($a, $b) ==> $b <=> $a,
        dict[
          4 => varray['daenerys', 'targaryen'],
          0 => varray['eddard', 'stark'],
          1 => varray['arya', 'stark'],
          2 => varray['tyrion', 'lannister'],
          3 => varray['joffrey', 'boratheon'],
        ],
      ),
    ];
  }

  <<DataProvider('provideTestSortBy')>>
  public function testSortBy<Tk as arraykey, Tv, Ts>(
    KeyedTraversable<Tk, Tv> $traversable,
    (function(Tv): Ts) $scalar_func,
    ?(function(Ts, Ts): int) $scalar_comparator,
    dict<Tk, Tv> $expected,
  ): void {
    expect(Dict\sort_by($traversable, $scalar_func, $scalar_comparator))
      ->toBeSame($expected);
  }

  public static function provideTestSortByKey(): varray<mixed> {
    return varray[
      tuple(
        Map {
          'the' => 'the',
          'quick' => 'quick',
          'brown' => 'brown',
          'fox' => 'fox',
          'jumped' => 'jumped',
        },
        null,
        dict[
          'brown' => 'brown',
          'fox' => 'fox',
          'jumped' => 'jumped',
          'quick' => 'quick',
          'the' => 'the',
        ],
      ),
      tuple(
        Vector {'the', 'quick', 'brown', 'fox', 'jumped'},
        ($a, $b) ==> $b <=> $a,
        dict[
          4 => 'jumped',
          3 => 'fox',
          2 => 'brown',
          1 => 'quick',
          0 => 'the',
        ],
      )
    ];
  }

  <<DataProvider('provideTestSortByKey')>>
  public function testSortByKey<Tk as arraykey, Tv>(
    KeyedTraversable<Tk, Tv> $traversable,
    ?(function(Tk, Tk): int) $key_comparator,
    dict<Tk, Tv> $expected,
  ): void {
    expect(Dict\sort_by_key($traversable, $key_comparator))
      ->toBeSame($expected);
  }
}
