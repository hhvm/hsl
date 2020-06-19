<?hh // strict
/*
 *  Copyright (c) 2004-present, Facebook, Inc.
 *  All rights reserved.
 *
 *  This source code is licensed under the MIT license found in the
 *  LICENSE file in the root directory of this source tree.
 *
 */

use namespace HH\Lib\{Dict, Str, Vec};
use function Facebook\FBExpect\expect; // @oss-enable
use type Facebook\HackTest\{DataProvider, HackTest}; // @oss-enable

// @oss-disable: <<Oncalls('hack')>>
final class DictOrderTest extends HackTest {

  public static function provideTestReverse(): vec<(KeyedTraversable<mixed, mixed>, dict<arraykey, mixed>)> {
    return vec[
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
        HackLibTestTraversables::getKeyedIterator(Vec\range(1, 5)),
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
    expect(Dict\reverse($traversable))->toEqual($expected);
  }

  public static function provideTestShuffle(): vec<(KeyedTraversable<mixed, mixed>)> {
    return vec[
      tuple(darray['0' => '0', '1' => '1', '2' => '2', '3' => '3']),
      tuple(dict['3' => 3, '2' => 2, '1' => 1, '0' => 0]),
      tuple(darray[
        'brown' => 'brown',
        'fox' => 'fox',
        'jumped' => 'jumped',
        'quick' => 'quick',
        'the' => 'the',
      ]),
      tuple(Map {
        'foo' => 'oof',
        'bar' => 'rab',
        'baz' => 'zab',
        'qux' => 'xuq',
        'yap' => 'pay',
      }),
    ];
  }

  <<DataProvider('provideTestShuffle')>>
  public function testShuffle<Tk as arraykey, Tv>(
    KeyedTraversable<Tk, Tv> $input,
  ): void {
    for ($i = 0; $i < 1000; $i++) {
      $shuffled1 = Dict\shuffle($input);
      $shuffled2 = Dict\shuffle($input);
      $dict_input = dict($input);

      expect(Dict\sort($shuffled1))->toEqual(Dict\sort($dict_input));
      expect(Dict\sort($shuffled2))->toEqual(Dict\sort($dict_input));

      // There is a chance that even if we shuffle we get the same thing twice.
      // That is ok but if we try 1000 times we should get different things.
      if (
        $shuffled1 !== $shuffled2 &&
        $shuffled1 !== $dict_input &&
        $shuffled2 !== $dict_input
      ) {
        return;
      }
    }

    self::fail('We shuffled 1000 times and the value never changed');
  }

  public static function provideTestSort(): vec<(KeyedTraversable<mixed, mixed>, ?(function(nothing, nothing): int), dict<arraykey, mixed>)> {
    return vec[
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
        /* HH_FIXME[4297] The type of the lambda argument(s) could not be inferred */
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
        /* HH_FIXME[4297] The type of the lambda argument(s) could not be inferred */
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
    expect(Dict\sort($traversable, $value_comparator))->toEqual($expected);
  }

  public static function provideTestSortBy(): vec<(KeyedTraversable<mixed, mixed>, (function(nothing): mixed), ?(function(nothing, nothing): int), dict<arraykey, mixed>)> {
    return vec[
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
      ->toEqual($expected);
  }

  public static function provideTestSortByKey(): vec<(KeyedTraversable<mixed, mixed>, ?(function(nothing, nothing): int), dict<arraykey, mixed>)> {
    return vec[
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
      ->toEqual($expected);
  }
}
