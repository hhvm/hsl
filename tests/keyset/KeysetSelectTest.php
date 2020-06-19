<?hh // strict
/*
 *  Copyright (c) 2004-present, Facebook, Inc.
 *  All rights reserved.
 *
 *  This source code is licensed under the MIT license found in the
 *  LICENSE file in the root directory of this source tree.
 *
 */

use namespace HH\Lib\{Keyset, Str, Vec};
use function Facebook\FBExpect\expect; // @oss-enable
use type Facebook\HackTest\{DataProvider, HackTest}; // @oss-enable

// @oss-disable: <<Oncalls('hack')>>
final class KeysetSelectTest extends HackTest {

  public static function provideTestDiff(): vec<(Traversable<mixed>, Traversable<mixed>, Container<Container<mixed>>, keyset<arraykey>)> {
    return vec[
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
        new Vector(Vec\range(0, 20)),
        Set {1, 3, 5},
        varray[
          Map {'foo' => 7, 'bar' => 9},
          Vec\range(11, 30),
        ],
        keyset[0, 2, 4, 6, 8, 10],
      ),
    ];
  }

  <<DataProvider('provideTestDiff')>>
  public function testDiff<Tv as arraykey>(
    Traversable<Tv> $first,
    Traversable<Tv> $second,
    Container<Container<Tv>> $rest,
    keyset<Tv> $expected,
  ): void {
    expect(Keyset\diff($first, $second, ...$rest))
      ->toEqual($expected);
  }

  public static function provideDrop(): vec<(Traversable<mixed>, int, keyset<arraykey>)> {
    return vec[
      tuple(
        vec[],
        5,
        keyset[],
      ),
      tuple(
        Vec\range(0, 5),
        0,
        keyset[0, 1, 2, 3, 4, 5],
      ),
      tuple(
        new Vector(Vec\range(0, 5)),
        10,
        keyset[],
      ),
      tuple(
        new Set(Vec\range(0, 5)),
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
    expect(Keyset\drop($traversable, $n))->toEqual($expected);
  }

  public static function provideTestFilter(): vec<(Traversable<mixed>, (function(nothing): bool), keyset<arraykey>)> {
    return vec[
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
        Vec\range(1, 10),
        $x ==> $x % 2 === 0,
        keyset[2, 4, 6, 8, 10],
      ),
      tuple(
        Map {'donald' => 'duck', 'daffy' => 'duck', 'mickey' => 'mouse'},
        $x ==> $x === 'duck',
        keyset['duck'],
      ),
      tuple(
        HackLibTestTraversables::getIterator(Vec\range(0, 5)),
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
      ->toEqual($expected);
  }

  public function testFilterWithoutPredicate(): void {
    expect(
      Keyset\filter(varray[0, 3, 5, 40, '', '0', 'win!'])
    )->toEqual(keyset[3, 5, 40, 'win!']);
  }

  // ?nonnull === mixed for DataProviderTypesLinter
  public static function provideTestFilterNulls(): vec<(Traversable<?nonnull>, keyset<arraykey>)> {
    return vec[
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
    expect(Keyset\filter_nulls($traversable))->toEqual($expected);
  }

  public static function provideTestFilterWithKey(): dict<string, (KeyedTraversable<mixed, mixed>, (function(nothing, nothing): bool), keyset<arraykey>)> {
    return dict[
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
        HackLibTestTraversables::getKeyedIterator(
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
    expect($result)->toEqual($expected);
  }

  public static function provideTestKeys(): vec<(KeyedTraversable<mixed, mixed>, keyset<arraykey>)> {
    return vec[
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
    expect(Keyset\keys($traversable))->toEqual($expected);
  }

  public static function provideTestIntersect(): vec<(Traversable<mixed>, Traversable<mixed>, Container<Container<mixed>>, keyset<arraykey>)> {
    return vec[
      tuple(
        Vec\range(0, 1000),
        varray[],
        varray[],
        keyset[],
      ),
      tuple(
        Vec\range(1, 10),
        Vec\range(1, 5),
        varray[
          Vec\range(2, 6),
          Vec\range(3, 7),
        ],
        keyset[3, 4, 5],
      ),
      tuple(
        Set {},
        Vec\range(1, 100),
        varray[],
        keyset[],
      ),
      tuple(
        Vec\range(1, 1000),
        Map {},
        varray[
          Set {},
          Vector {},
        ],
        keyset[],
      ),
      tuple(
        new Vector(Vec\range(1, 100)),
        Map {1 => 2, 39 => 40},
        varray[
          Vec\range(0, 40),
        ],
        keyset[2, 40],
      ),
      tuple(
        varray[3, 4, 4, 5],
        varray[3, 4],
        varray[],
        keyset[3, 4],
      ),
      tuple(
        vec[1, 2, 3, 4],
        vec[1, 2, 3, 4],
        vec[
          vec[],
          vec[1, 2, 3, 4],
        ],
        keyset[],
      ),
    ];
  }

  <<DataProvider('provideTestIntersect')>>
  public function testIntersect<Tv as arraykey>(
    Traversable<Tv> $first,
    Traversable<Tv> $second,
    Container<Container<Tv>> $rest,
    keyset<Tv> $expected,
  ): void {
    expect(Keyset\intersect($first, $second, ...$rest))
      ->toEqual($expected);
  }

  public static function provideTake(): vec<(Traversable<mixed>, int, keyset<arraykey>)> {
    return vec[
      tuple(
        keyset[],
        5,
        keyset[],
      ),
      tuple(
        Vec\range(0, 5),
        0,
        keyset[],
      ),
      tuple(
        new Vector(Vec\range(0, 5)),
        10,
        keyset[0, 1, 2, 3, 4, 5],
      ),
      tuple(
        new Set(Vec\range(0, 5)),
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
    expect(Keyset\take($traversable, $n))->toEqual($expected);
  }

  public function testTakeIter(): void {
    $iter = HackLibTestTraversables::getIterator(Vec\range(0, 4));
    expect(Keyset\take($iter, 2))->toEqual(keyset[0, 1]);
    expect(Keyset\take($iter, 0))->toEqual(keyset[]);
    expect(Keyset\take($iter, 2))->toEqual(keyset[2, 3]);
    expect(Keyset\take($iter, 2))->toEqual(keyset[4]);
  }
}
