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
use function Facebook\FBExpect\expect;
use type Facebook\HackTest\HackTestCase; // @oss-enable

/**
 * @emails oncall+hack
 */
final class KeysetAsyncTest extends HackTestCase {

  public static function provideTestGen(): varray<mixed> {
    return varray[
      tuple(
        Vector {
          async {return 'the';},
          async {return 'quick';},
          async {return 'brown';},
          async {return 'fox';},
        },
        keyset['the', 'quick', 'brown', 'fox'],
      ),
      tuple(
        Map {
          'foo' => async {return 1;},
          'bar' => async {return 2;},
        },
        keyset[1, 2],
      ),
      tuple(
        HackLibTestTraversables::getIterator(varray[
          async {return 'the';},
          async {return 'quick';},
          async {return 'brown';},
          async {return 'fox';},
        ]),
        keyset['the', 'quick', 'brown', 'fox'],
      ),
    ];
  }

  <<DataProvider('provideTestGen')>>
  public function testFromAsync<Tv as arraykey>(
    Traversable<Awaitable<Tv>> $awaitables,
    keyset<Tv> $expected,
  ): void {
    /* HH_IGNORE_ERROR[5542] open source */
    \HH\Asio\join(async {
      $actual = await Keyset\from_async($awaitables);
      expect($actual)->toBeSame($expected);
    });
  }

  public static function provideTestGenMap(): varray<mixed> {
    return varray[
      tuple(
        keyset[1,2,3],
        async ($num) ==> $num * 2,
        keyset[2,4,6],
      ),
      tuple(
        vec[1,1,1,2,2,3],
        async ($num) ==> $num * 2,
        keyset[2,4,6],
      ),
      tuple(
        varray['dan', 'danny', 'daniel'],
        async ($word) ==> strrev($word),
        keyset['nad', 'ynnad', 'leinad'],
      ),
    ];
  }

  <<DataProvider('provideTestGenMap')>>
  public function testMapAsync<Tv>(
    Traversable<Tv> $traversable,
    (function(Tv): Awaitable<arraykey>) $async_func,
    keyset<arraykey> $expected,
  ): void {
    /* HH_IGNORE_ERROR[5542] open source */
    \HH\Asio\join(async {
      $actual = await Keyset\map_async($traversable, $async_func);
      expect($actual)->toBeSame($expected);
    });
  }

  public static function provideTestGenFilter(): varray<(
    Container<arraykey>,
    (function(arraykey): Awaitable<bool>),
    keyset<arraykey>,
  )> {
    return varray[
      tuple(
        darray[
          2 => 'two',
          4 => 'four',
          6 => 'six',
          8 => 'eight',
        ],
        async ($word) ==> strlen($word) % 2 === 1,
        keyset['two', 'six', 'eight'],
      ),
      tuple(
        Vector {'jumped', 'over', 'jumped'},
        async ($word) ==> strlen($word) % 2 === 0,
        keyset['jumped', 'over'],
      ),
      tuple(
        Set {'the', 'quick', 'brown', 'fox', 'jumped', 'over'},
        async ($word) ==> strlen($word) % 2 === 0,
        keyset['jumped', 'over'],
      ),
    ];
  }

  <<DataProvider('provideTestGenFilter')>>
  public function testFilterAsync<Tv as arraykey>(
    Container<Tv> $traversable,
    (function(Tv): Awaitable<bool>) $async_predicate,
    keyset<Tv> $expected,
  ): void {
    /* HH_IGNORE_ERROR[5542] open source */
    \HH\Asio\join(async {
      $actual = await Keyset\filter_async($traversable, $async_predicate);
      expect($actual)->toBeSame($expected);
    });
  }
}
