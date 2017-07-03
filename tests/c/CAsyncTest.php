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

use \HH\Lib\C as C;
use function \Facebook\FBExpect\expect;
// @oss-disable: use InvariantViolationException as InvariantException;

/**
 * @emails oncall+hack_prod_infra
 */
final class CAsyncTest extends PHPUnit_Framework_TestCase {

  public static function provideTestGenFirst(): array<mixed> {
    return array(
      tuple(
        async {
          return array();
        },
        null,
      ),
      tuple(
        async {
          return HackLibTestTraversables::getIterator(range(1, 5));
        },
        1,
      ),
      tuple(
        async {
          return dict[
            '5' => '10',
            '10' => '20',
          ];
        },
        '10',
      ),
    );
  }

  /** @dataProvider provideTestGenFirst */
  public async function testGenFirst<T>(
    Awaitable<Traversable<T>> $awaitable,
    ?T $expected,
  ): Awaitable<void> {
    $actual = await C\gen_first($awaitable);
    expect($actual)->toBeSame($expected);
  }

  public static function provideTestGenFirstx(): array<mixed> {
    return array(
      tuple(
        async {
          return array();
        },
        InvariantException::class,
      ),
      tuple(
        async {
          return HackLibTestTraversables::getIterator(range(1, 5));
        },
        1,
      ),
      tuple(
        async {
          return dict[
            '5' => '10',
            '10' => '20',
          ];
        },
        '10',
      ),
    );
  }

  /** @dataProvider provideTestGenFirstx */
  public async function testGenFirstx<T>(
    Awaitable<Traversable<T>> $awaitable,
    mixed $expected,
  ): Awaitable<void> {
    if (is_subclass_of($expected, Exception::class)) {
      expect(
        async () ==> await C\gen_firstx($awaitable),
      )->toThrow(/* UNSAFE_EXPR */ $expected);
    } else {
      $actual = await C\gen_firstx($awaitable);
      expect($actual)->toBeSame($expected);
    }
  }
}
