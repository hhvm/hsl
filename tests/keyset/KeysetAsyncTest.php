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

use namespace HH\Lib\Keyset as KeysetHSL;
use function Facebook\FBExpect\expect;

/**
 * @emails oncall+hack_prod_infra
 */
final class KeysetAsyncTest extends PHPUnit_Framework_TestCase {

  public static function provideTestGenMap(): array<mixed> {
    return array(
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
        array('dan', 'danny', 'daniel'),
        async ($word) ==> strrev($word),
        keyset['nad', 'ynnad', 'leinad'],
      ),
    );
  }

  /** @dataProvider provideTestGenMap */
  public function testMapAsync<Tv>(
    Traversable<Tv> $traversable,
    (function(Tv): Awaitable<arraykey>) $async_func,
    keyset<arraykey> $expected,
  ): void {
    /* HH_IGNORE_ERROR[5542] open source */
    \HH\Asio\join(async {
      $actual = await KeysetHSL\map_async($traversable, $async_func);
      expect($actual)->toBeSame($expected);
    });
  }

  public static function provideTestGenFilter(): array<(
    Container<arraykey>,
    (function(arraykey): Awaitable<bool>),
    keyset<arraykey>,
  )> {
    return array(
      tuple(
        array(
          '2' => 'two',
          '4' => 'four',
          6 => 'six',
          '8' => 'eight',
        ),
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
    );
  }

  /** @dataProvider provideTestGenFilter */
  public function testFilterAsync<Tv as arraykey>(
    Container<Tv> $traversable,
    (function(Tv): Awaitable<bool>) $async_predicate,
    keyset<Tv> $expected,
  ): void {
    /* HH_IGNORE_ERROR[5542] open source */
    \HH\Asio\join(async {
      $actual = await KeysetHSL\filter_async($traversable, $async_predicate);
      expect($actual)->toBeSame($expected);
    });
  }
}
