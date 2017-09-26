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

use namespace HH\Lib\C as C;
use function Facebook\FBExpect\expect;
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
  public function testFirstAsync<T>(
    Awaitable<Traversable<T>> $awaitable,
    ?T $expected,
  ): void {
    /* HH_IGNORE_ERROR[5542] open source */
    \HH\Asio\join(async {
      $actual = await C\first_async($awaitable);
      expect($actual)->toBeSame($expected);
    });
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
  public function testFirstxAsync<T>(
    Awaitable<Traversable<T>> $awaitable,
    mixed $expected,
  ): void {
    /* HH_IGNORE_ERROR[5542] open source */
    \HH\Asio\join(async {
      if (is_subclass_of($expected, Exception::class)) {
        expect(
          async () ==> await C\firstx_async($awaitable),
        )->toThrow(/* UNSAFE_EXPR */ $expected);
      } else {
        $actual = await C\firstx_async($awaitable);
        expect($actual)->toBeSame($expected);
      }
    });
  }
}
