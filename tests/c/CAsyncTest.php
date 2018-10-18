<?hh // strict
/*
 *  Copyright (c) 2004-present, Facebook, Inc.
 *  All rights reserved.
 *
 *  This source code is licensed under the MIT license found in the
 *  LICENSE file in the root directory of this source tree.
 *
 */

use namespace HH\Lib\{C, Vec};
use function Facebook\FBExpect\expect;
use type Facebook\HackTest\DataProvider; // @oss-enable
use type Facebook\HackTest\HackTest as HackTestCase; // @oss-enable
// @oss-disable: use InvariantViolationException as InvariantException;

// @oss-disable: <<Oncalls('hack')>>
final class CAsyncTest extends HackTestCase {

  public static function provideTestGenFirst(
  ): varray<(Awaitable<Traversable<mixed>>, mixed)> {
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

  <<DataProvider('provideTestGenFirst')>>
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

  public static function provideTestGenFirstx(
  ): varray<(Awaitable<Traversable<mixed>>, mixed)> {
    return varray[
      tuple(
        async {
          return HackLibTestTraversables::getIterator(Vec\range(1, 5));
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

  <<DataProvider('provideTestGenFirstx')>>
  public function testFirstxAsync<T>(
    Awaitable<Traversable<T>> $awaitable,
    T $expected,
  ): void {
    /* HH_IGNORE_ERROR[5542] open source */
    \HH\Asio\join(async {
      $actual = await C\firstx_async($awaitable);
      expect($actual)->toBeSame($expected);
    });
  }

  public static function provideTestGenFirstxException<T>(
  ): varray<(Awaitable<Traversable<T>>, classname<Exception>)> {
    return varray[
      tuple(
        async {
          return varray[];
        },
        InvariantException::class,
      ),
    ];
  }

  <<DataProvider('provideTestGenFirstxException')>>
  public function testFirstxExceptionAsync<T>(
    Awaitable<Traversable<T>> $awaitable,
    classname<Exception> $expected,
  ): void {
    /* HH_IGNORE_ERROR[5542] open source */
    \HH\Asio\join(async {
      expect(
        async () ==> await C\firstx_async($awaitable),
      )->toThrow($expected);
    });
  }
}
