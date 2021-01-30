<?hh // strict
/*
 *  Copyright (c) 2004-present, Facebook, Inc.
 *  All rights reserved.
 *
 *  This source code is licensed under the MIT license found in the
 *  LICENSE file in the root directory of this source tree.
 *
 */

use namespace HH\Lib\{Str, Vec};
use function Facebook\FBExpect\expect; // @oss-enable
use type Facebook\HackTest\{DataProvider, HackTest}; // @oss-enable

// @oss-disable: <<Oncalls('hack')>>
final class VecAsyncTest extends HackTest {

  public static function provideTestFromAsync(): varray<mixed> {
    return varray[
      tuple(
        Vector {
          async {return 'the';},
          async {return 'quick';},
          async {return 'brown';},
          async {return 'fox';},
        },
        vec['the', 'quick', 'brown', 'fox'],
      ),
      tuple(
        Map {
          'foo' => async {return 1;},
          'bar' => async {return 2;},
        },
        vec[1, 2],
      ),
      tuple(
        HackLibTestTraversables::getIterator(varray[
          async {return 'the';},
          async {return 'quick';},
          async {return 'brown';},
          async {return 'fox';},
        ]),
        vec['the', 'quick', 'brown', 'fox'],
      ),
    ];
  }

  <<DataProvider('provideTestFromAsync')>>
  public async function testFromAsync<Tv>(
    Traversable<Awaitable<Tv>> $awaitables,
    vec<Tv> $expected,
  ): Awaitable<void> {
    $actual = await Vec\from_async($awaitables);
    expect($actual)->toEqual($expected);
  }

  public static function provideTestFilterAsync(): varray<mixed> {
    return varray[
      tuple(
        darray[
          2 => 'two',
          4 => 'four',
          6 => 'six',
          8 => 'eight',
        ],
        async ($word) ==> Str\length($word) % 2 === 1,
        vec['two', 'six', 'eight'],
      ),
      tuple(
        Vector {'the', 'quick', 'brown', 'fox', 'jumped', 'over'},
        async ($word) ==> Str\length($word) % 2 === 0,
        vec['jumped', 'over'],
      ),
    ];
  }

  <<DataProvider('provideTestFilterAsync')>>
  public async function testFilterAsync<Tv>(
    Container<Tv> $container,
    (function(Tv): Awaitable<bool>) $value_predicate,
    vec<Tv> $expected,
  ): Awaitable<void> {
    $actual = await Vec\filter_async($container, $value_predicate);
    expect($actual)->toEqual($expected);
  }

  public static function provideTestMapAsync(): varray<mixed> {
    return varray[
      tuple(
        Vector {'the', 'quick', 'brown', 'fox'},
        async ($word) ==> Str\reverse($word),
        vec['eht', 'kciuq', 'nworb', 'xof'],
      ),
      tuple(
        HackLibTestTraversables::getIterator(
          varray['the', 'quick', 'brown', 'fox'],
        ),
        async ($word) ==> Str\reverse($word),
        vec['eht', 'kciuq', 'nworb', 'xof'],
      ),
      tuple(
        dict['one' => 'uno', 'two' => 'due', 'three' => 'tre'],
        async ($word) ==> Str\reverse($word),
        vec['onu', 'eud', 'ert'],
      ),
    ];
  }

  <<DataProvider('provideTestMapAsync')>>
  public async function testMapAsync<Tk, Tv>(
    Traversable<Tk> $keys,
    (function(Tk): Awaitable<Tv>) $async_func,
    vec<Tv> $expected,
  ): Awaitable<void> {
    $actual = await Vec\map_async($keys, $async_func);
    expect($actual)->toEqual($expected);
  }
}
