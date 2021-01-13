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
use function Facebook\FBExpect\expect; // @oss-enable
use type Facebook\HackTest\{DataProvider, HackTest}; // @oss-enable
// @oss-disable: use InvariantViolationException as InvariantException;

// @oss-disable: <<Oncalls('hack')>>
final class CAsyncTest extends HackTest {

  public static function provideTestGenFirstx(
  ): vec<(Awaitable<Traversable<mixed>>, mixed)> {
    return vec[
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
  public async function testFirstxAsync<T>(
    Awaitable<Traversable<T>> $awaitable,
    T $expected,
  ): Awaitable<void> {
    $actual = await C\firstx_async($awaitable);
    expect($actual)->toEqual($expected);
  }

  public static function provideTestGenFirstxException(
  ): vec<(Awaitable<Traversable<nothing>>, classname<Exception>)> {
    return vec[
      tuple(
        async {
          return varray[];
        },
        InvariantException::class,
      ),
    ];
  }

  <<DataProvider('provideTestGenFirstxException')>>
  public async function testFirstxExceptionAsync<T>(
    Awaitable<Traversable<T>> $awaitable,
    classname<Exception> $expected,
  ): Awaitable<void> {
    expect(
      async () ==> await C\firstx_async($awaitable),
    )->toThrow($expected);
  }
}
