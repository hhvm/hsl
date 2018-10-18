<?hh // strict
/*
 *  Copyright (c) 2004-present, Facebook, Inc.
 *  All rights reserved.
 *
 *  This source code is licensed under the MIT license found in the
 *  LICENSE file in the root directory of this source tree.
 *
 */

use namespace HH\Lib\{Keyset, Str};
use function Facebook\FBExpect\expect;
use type Facebook\HackTest\DataProvider; // @oss-enable
use type Facebook\HackTest\HackTest as HackTestCase; // @oss-enable

// @oss-disable: <<Oncalls('hack')>>
final class KeysetSelectTest extends HackTestCase {

  public static function provideTestDiff(): varray<mixed> {
    return varray[
      tuple(
        varray[],
        varray[],
        varray[],
        keyset[],
      ),
      tuple(
        vec[1, 3, 5, 7],
        dict[],
        varray[],
        keyset[1, 3, 5, 7],
      ),
      tuple(
        new Vector(range(0, 20)),
        Set {1, 3, 5},
        varray[
          Map {'foo' => 7, 'bar' => 9},
          HackLibTestTraversables::getIterator(range(11, 30)),
        ],
        keyset[0, 2, 4, 6, 8, 10],
      ),
    ];
  }

  <<DataProvider('provideTestDiff')>>
  public function testDiff<Tv as arraykey>(
    Traversable<Tv> $first,
    Traversable<Tv> $second,
    Container<Traversable<Tv>> $rest,
    keyset<Tv> $expected,
  ): void {
    expect(Keyset\diff($first, $second, ...$rest))
      ->toBeSame($expected);
  }

  public static function provideDrop(): varray<mixed> {
    return varray[
      tuple(
        vec[],
        5,
        keyset[],
      ),
      tuple(
        range(0, 5),
        0,
        keyset[0, 1, 2, 3, 4, 5],
      ),
      tuple(
        new Vector(range(0, 5)),
        10,
        keyset[],
      ),
      tuple(
        new Set(range(0, 5)),
        2,
        keyset[2, 3, 4, 5],
      ),
      tuple(
        HackLibTestTraversables::getIterator(varray[0, 1, 2, 3, 4, 5, 5, 5]),
        5,
        keyset[5],
      ),
    ];
  }

  <<DataProvider('provideDrop')>>
  public function testDrop<Tv as arraykey>(
    Traversable<Tv> $traversable,
    int $n,
    keyset<Tv> $expected,
  ): void {
    expect(Keyset\drop($traversable, $n))->toBeSame($expected);
  }

  public static function provideTestFilter(): varray<mixed> {
    return varray[
      tuple(
        varray[],
        $x ==> true,
        keyset[],
      ),
      tuple(
        dict[0 => 1],
        $x ==> true,
        keyset[1],
      ),
      tuple(
        dict[0 => 1],
        $x ==> false,
        keyset[],
      ),
      tuple(
        range(1, 10),
        $x ==> $x % 2 === 0,
        keyset[2, 4, 6, 8, 10],
      ),
      tuple(
        Map {'donald' => 'duck', 'daffy' => 'duck', 'mickey' => 'mouse'},
        $x ==> $x === 'duck',
        keyset['duck'],
      ),
      tuple(
        HackLibTestTraversables::getIterator(range(0, 5)),
        $x ==> $x % 2 === 0,
        keyset[0, 2, 4],
      ),
    ];
  }

  <<DataProvider('provideTestFilter')>>
  public function testFilter<Tv as arraykey>(
    Traversable<Tv> $traversable,
    (function(Tv): bool) $predicate,
    keyset<Tv> $expected,
  ): void {
    expect(Keyset\filter($traversable, $predicate))
      ->toBeSame($expected);
  }

  public function testFilterWithoutPredicate(): void {
    expect(
      Keyset\filter(varray[0, 3, 5, 40, '', '0', 'win!'])
    )->toBeSame(keyset[3, 5, 40, 'win!']);
  }

  public static function provideTestFilterNulls(): varray<mixed> {
    return varray[
      tuple(
        varray[null, null, null],
        keyset[],
      ),
      tuple(
        Map {
          'bar' => null,
          'baz' => '',
          'qux' => 0,
        },
        keyset['', 0],
      ),
      tuple(
        Vector {
          'foo',
          'bar',
          null,
          'baz',
        },
        keyset['foo', 'bar', 'baz'],
      ),
      tuple(
        HackLibTestTraversables::getKeyedIterator(darray[
          1 => null,
          2 => 1,
          3 => '0',
        ]),
        keyset[1, '0'],
      ),
    ];
  }

  <<DataProvider('provideTestFilterNulls')>>
  public function testFilterNulls<Tv as arraykey>(
    Traversable<?Tv> $traversable,
    keyset<Tv> $expected,
  ): void {
    expect(Keyset\filter_nulls($traversable))->toBeSame($expected);
  }

  public static function provideTestFilterWithKey(): darray<string, mixed> {
    return darray[
      'All elements selected' => tuple(
        vec['the', 'quick', 'brown', 'fox', 'jumped'],
        ($key, $value) ==> true,
        keyset['the', 'quick', 'brown', 'fox', 'jumped'],
      ),
      'No elements selected' => tuple(
        vec['the', 'quick', 'brown', 'fox', 'jumped'],
        ($key, $value) ==> false,
        keyset[],
      ),
      'odd elements selected' => tuple(
        vec['the', 'quick', 'brown', 'fox', 'jumped'],
        ($key, $value) ==> $key % 2 === 1,
        keyset['quick','fox'],
      ),
      'elements selected starting with "f"' => tuple(
        vec['the', 'quick', 'brown', 'fox', 'jumped'],
        ($key, $value) ==> Str\starts_with($value, 'f'),
        keyset['fox'],
      ),
      'elements selected starting with "f" 2' => tuple(
        HackLibTestTraversables::getIterator(
          vec['the', 'quick', 'brown', 'fox', 'jumped']
        ),
        ($key, $value) ==> Str\starts_with($value, 'f'),
        keyset['fox'],
      ),
    ];
  }

  <<DataProvider('provideTestFilterWithKey')>>
  public function testFilterWithKey<Tk, Tv as arraykey>(
    KeyedTraversable<Tk, Tv> $traversable,
    (function(Tk, Tv): bool) $filter_func,
    keyset<Tv> $expected,
  ): void {
    $result = Keyset\filter_with_key($traversable, $filter_func);
    expect($result)->toBeSame($expected);
  }

  public static function provideTestKeys(): varray<mixed> {
    return varray[
      tuple(
        Map {},
        keyset[],
      ),
      tuple(
        dict[
          2 => 4,
          4 => 8,
          6 => 12,
          8 => 16,
        ],
        keyset[2, 4, 6, 8],
      ),
      tuple(
        HackLibTestTraversables::getKeyedIterator(darray[
          2 => 4,
          4 => 8,
          6 => 12,
          8 => 16,
        ]),
        keyset[2, 4, 6, 8],
      ),
    ];
  }

  <<DataProvider('provideTestKeys')>>
  public function testKeys<Tk as arraykey, Tv>(
    KeyedTraversable<Tk, Tv> $traversable,
    keyset<Tk> $expected,
  ): void {
    expect(Keyset\keys($traversable))->toBeSame($expected);
  }

  public static function provideTestIntersect(): varray<mixed> {
    return varray[
      tuple(
        range(0, 1000),
        varray[],
        varray[],
        keyset[],
      ),
      tuple(
        range(1, 10),
        range(1, 5),
        varray[
          range(2, 6),
          range(3, 7),
        ],
        keyset[3, 4, 5],
      ),
      tuple(
        Set {},
        range(1, 100),
        varray[],
        keyset[],
      ),
      tuple(
        range(1, 1000),
        Map {},
        varray[
          Set {},
          Vector {},
        ],
        keyset[],
      ),
      tuple(
        new Vector(range(1, 100)),
        Map {1 => 2, 39 => 40},
        varray[
          HackLibTestTraversables::getIterator(range(0, 40)),
        ],
        keyset[2, 40],
      ),
      tuple(
        varray[3, 4, 4, 5],
        varray[3, 4],
        varray[],
        keyset[3, 4],
      ),
    ];
  }

  <<DataProvider('provideTestIntersect')>>
  public function testIntersect<Tv as arraykey>(
    Traversable<Tv> $first,
    Traversable<Tv> $second,
    Container<Traversable<Tv>> $rest,
    keyset<Tv> $expected,
  ): void {
    expect(Keyset\intersect($first, $second, ...$rest))
      ->toBeSame($expected);
  }

  public static function provideTake(): varray<mixed> {
    return varray[
      tuple(
        keyset[],
        5,
        keyset[],
      ),
      tuple(
        range(0, 5),
        0,
        keyset[],
      ),
      tuple(
        new Vector(range(0, 5)),
        10,
        keyset[0, 1, 2, 3, 4, 5],
      ),
      tuple(
        new Set(range(0, 5)),
        2,
        keyset[0, 1],
      ),
      tuple(
        HackLibTestTraversables::getIterator(varray[0, 0, 1, 1, 2, 2, 3, 3]),
        5,
        keyset[0, 1, 2],
      ),
    ];
  }

  <<DataProvider('provideTake')>>
  public function testTake<Tv as arraykey>(
    Traversable<Tv> $traversable,
    int $n,
    keyset<Tv> $expected,
  ): void {
    expect(Keyset\take($traversable, $n))->toBeSame($expected);
  }

  public function testTakeIter(): void {
    $iter = HackLibTestTraversables::getIterator(range(0, 4));
    expect(Keyset\take($iter, 2))->toBeSame(keyset[0, 1]);
    expect(Keyset\take($iter, 0))->toBeSame(keyset[]);
    expect(Keyset\take($iter, 2))->toBeSame(keyset[2, 3]);
    expect(Keyset\take($iter, 2))->toBeSame(keyset[4]);
  }
}
