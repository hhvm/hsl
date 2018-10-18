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
use function Facebook\FBExpect\expect;
// @oss-disable: use InvariantViolationException as InvariantException;
use type Facebook\HackTest\DataProvider; // @oss-enable
use type Facebook\HackTest\HackTest as HackTestCase; // @oss-enable

// @oss-disable: <<Oncalls('hack')>>
final class VecTransformTest extends HackTestCase {

  public static function provideTestChunk(): varray<mixed> {
    return varray[
      tuple(
        Map {},
        10,
        vec[],
      ),
      tuple(
        varray[0, 1, 2, 3, 4],
        2,
        vec[
          vec[0, 1],
          vec[2, 3],
          vec[4],
        ],
      ),
      tuple(
        HackLibTestTraversables::getKeyedIterator(
          darray['foo' => 'bar', 'baz' => 'qux'],
        ),
        1,
        vec[
          vec['bar'],
          vec['qux'],
        ],
      ),
    ];
  }

  <<DataProvider('provideTestChunk')>>
  public function testChunk<Tv>(
    Traversable<Tv> $traversable,
    int $size,
    vec<vec<Tv>> $expected,
  ): void {
    expect(Vec\chunk($traversable, $size))->toBeSame($expected);
  }

  public static function provideTestFill(): varray<mixed> {
    return varray[
      tuple(
        0,
        42,
        vec[],
      ),
      tuple(
        4,
        4,
        vec[4, 4, 4, 4],
      ),
      tuple(
        2,
        darray['foo' => 'bar'],
        vec[
          darray['foo' => 'bar'],
          darray['foo' => 'bar'],
        ],
      ),
    ];
  }

  <<DataProvider('provideTestFill')>>
  public function testFill<Tv>(
    int $size,
    Tv $value,
    vec<Tv> $expected,
  ): void {
    expect(Vec\fill($size, $value))->toBeSame($expected);
  }

  public function testFillExceptions(): void {
    expect(() ==> Vec\fill(-1, true))->toThrow(InvariantException::class);
  }

  public static function provideTestFlatten(): varray<mixed> {
    return varray[
      tuple(
        varray[],
        vec[],
      ),
      tuple(
        varray[
          varray[], Vector {}, Map {}, Set {},
        ],
        vec[],
      ),
      tuple(
        varray[
          varray['the', 'quick'],
          Vector {'brown', 'fox'},
          Map {'jumped' => 'over'},
          HackLibTestTraversables::getIterator(varray['the', 'lazy', 'dog']),
        ],
        vec['the', 'quick', 'brown', 'fox', 'over', 'the', 'lazy', 'dog'],
      ),
    ];
  }

  <<DataProvider('provideTestFlatten')>>
  public function testFlatten<Tv>(
    Traversable<Traversable<Tv>> $traversables,
    vec<Tv> $expected,
  ): void {
    expect(Vec\flatten($traversables))->toBeSame($expected);
  }

  public static function provideTestMap(): varray<mixed> {
    $doubler = $x ==> $x * 2;
    return varray[
      tuple(
        varray[],
        $doubler,
        vec[],
      ),
      tuple(
        varray[1],
        $doubler,
        vec[2],
      ),
      tuple(
        range(10, 15),
        $doubler,
        vec[20, 22, 24, 26, 28, 30],
      ),
      tuple(
        varray['a'],
        $x ==> $x. ' buzz',
        vec['a buzz'],
      ),
      tuple(
        varray['a', 'bee', 'a bee'],
        $x ==> $x. ' buzz',
        vec['a buzz', 'bee buzz', 'a bee buzz'],
      ),
      tuple(
        dict[
          'donald' => 'duck',
          'daffy' => 'duck',
          'mickey' => 'mouse',
        ],
        $s ==> Str\reverse($s),
        vec['kcud', 'kcud', 'esuom'],
      ),
      tuple(
        Map {'donald' => 'duck', 'daffy' => 'duck', 'mickey' => 'mouse'},
        $s ==> Str\reverse($s),
        vec['kcud', 'kcud', 'esuom'],
      ),
      tuple(
        Vector {10, 20},
        $doubler,
        vec[20, 40],
      ),
      tuple(
        Set {10, 20},
        $doubler,
        vec[20, 40],
      ),
      tuple(
        keyset[10, 20],
        $doubler,
        vec[20, 40],
      ),
      tuple(
        HackLibTestTraversables::getIterator(varray[1, 2, 3]),
        $doubler,
        vec[2, 4, 6],
      ),
      tuple(
        HackLibTestTraversables::getKeyedIterator(darray[10 => 1, 20 => 2, 30 => 3]),
        $doubler,
        vec[2, 4, 6],
      ),
    ];
  }

  <<DataProvider('provideTestMap')>>
  public function testMap<Tv1, Tv2>(
    Traversable<Tv1> $traversable,
    (function(Tv1): Tv2) $value_func,
    vec<Tv2> $expected,
  ): void {
    expect(Vec\map($traversable, $value_func))->toBeSame($expected);
  }

  public static function provideTestMapWithKey(): varray<mixed> {
    return varray[
      tuple(
        varray[],
        ($a, $b) ==> null,
        vec[],
      ),
      tuple(
        Vector {'the', 'quick', 'brown', 'fox'},
        ($k, $v) ==> (string)$k.$v,
        vec['0the', '1quick', '2brown', '3fox'],
      ),
      tuple(
        HackLibTestTraversables::getKeyedIterator(range(1, 5)),
        ($k, $v) ==> $v * $k,
        vec[0, 2, 6, 12, 20],
      ),
    ];
  }

  <<DataProvider('provideTestMapWithKey')>>
  public function testMapWithKey<Tk, Tv1, Tv2>(
    KeyedTraversable<Tk, Tv1> $traversable,
    (function(Tk, Tv1): Tv2) $value_func,
    vec<Tv2> $expected,
  ): void {
    expect(Vec\map_with_key($traversable, $value_func))->toBeSame($expected);
  }

}
