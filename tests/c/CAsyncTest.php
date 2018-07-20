<?hh // strict
/*
 *  Copyright (c) 2004-present, Facebook, Inc.
 *  All rights reserved.
 *
 *  This source code is licensed under the MIT license found in the
 *  LICENSE file in the root directory of this source tree.
 *
 */

use namespace HH\Lib\C as C;
use function Facebook\FBExpect\expect;
use type Facebook\HackTest\HackTestCase; // @oss-enable
// @oss-disable: use InvariantViolationException as InvariantException;

/**
 * @emails oncall+hack
 */
final class CAsyncTest extends HackTestCase {

  public static function provideTestGenFirst(): varray<mixed> {
    return varray[
      tuple(
        async {
          return varray[];
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
    ];
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

  public static function provideTestGenFirstx(): varray<mixed> {
    return varray[
      tuple(
        async {
          return varray[];
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
    ];
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
