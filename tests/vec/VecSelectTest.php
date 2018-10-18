<?hh // strict
/*
 *  Copyright (c) 2004-present, Facebook, Inc.
 *  All rights reserved.
 *
 *  This source code is licensed under the MIT license found in the
 *  LICENSE file in the root directory of this source tree.
 *
 */

use namespace HH\Lib\{C, Math, Str, Vec};
use function Facebook\FBExpect\expect;
use type Facebook\HackTest\DataProvider; // @oss-enable
use type Facebook\HackTest\HackTest as HackTestCase; // @oss-enable

// @oss-disable: <<Oncalls('hack')>>
final class VecSelectTest extends HackTestCase {

  public static function provideTestDiff(): varray<mixed> {
    return varray[
      tuple(
        varray[],
        varray[
          darray[],
        ],
        vec[],
      ),
      tuple(
        new Vector(range(0, 20)),
        varray[
          Set {1, 3, 5},
          Map {'foo' => 7, 'bar' => 9},
          HackLibTestTraversables::getIterator(range(11, 30)),
        ],
        vec[0, 2, 4, 6, 8, 10],
      ),
    ];
  }

  <<DataProvider('provideTestDiff')>>
  public function testDiff<Tv as arraykey>(
    Traversable<Tv> $base,
    Container<Traversable<Tv>> $traversables,
    vec<Tv> $expected,
  ): void {
    /* HH_FIXME[4104] Stricter enforcement of argument unpacking arity (T25385321) */
    expect(Vec\diff($base, ...$traversables))->toBeSame($expected);
  }

  public static function provideTestDiffBy(): varray<mixed> {
    return varray[
      tuple(
        varray['the', 'quick', 'brown', 'fox'],
        varray[],
        $x ==> $x,
        vec['the', 'quick', 'brown', 'fox'],
      ),
      tuple(
        varray[],
        Vector {'the', 'quick', 'brown', 'fox'},
        $x ==> $x,
        vec[],
      ),
      tuple(
        Set {'plum', 'port', 'paste', 'pun', 'promise'},
        HackLibTestTraversables::getIterator(
          varray['power', 'push', 'pin', 'pygmy'],
        ),
        ($str) ==> Str\slice($str, 0, 2),
        vec['plum', 'paste', 'promise'],
      ),
    ];
  }

  <<DataProvider('provideTestDiffBy')>>
  public function testDiffBy<Tv, Ts as arraykey>(
    Traversable<Tv> $first,
    Traversable<Tv> $second,
    (function(Tv): Ts) $scalar_func,
    vec<Tv> $expected,
  ): void {
    expect(Vec\diff_by($first, $second, $scalar_func))
      ->toBeSame($expected);
  }

  public static function provideDrop(): varray<mixed> {
    return varray[
      tuple(
        vec[],
        5,
        vec[],
      ),
      tuple(
        range(0, 5),
        0,
        vec[0, 1, 2, 3, 4, 5],
      ),
      tuple(
        new Vector(range(0, 5)),
        10,
        vec[],
      ),
      tuple(
        new Set(range(0, 5)),
        2,
        vec[2, 3, 4, 5],
      ),
      tuple(
        HackLibTestTraversables::getIterator(range(0, 5)),
        5,
        vec[5],
      ),
    ];
  }

  <<DataProvider('provideDrop')>>
  public function testDrop<Tv>(
    Traversable<Tv> $traversable,
    int $n,
    vec<Tv> $expected,
  ): void {
    expect(Vec\drop($traversable, $n))->toBeSame($expected);
  }

  public static function provideTestFilter(): varray<mixed> {
    return varray[
      tuple(
        dict[],
        $x ==> true,
        vec[],
      ),
      tuple(
        dict[0 => 1],
        $x ==> true,
        vec[1],
      ),
      tuple(
        dict[0 => 1],
        $x ==> false,
        vec[],
      ),
      tuple(
        range(1, 10),
        $x ==> $x % 2 === 0,
        vec[2, 4, 6, 8, 10],
      ),
      tuple(
        Map {'donald' => 'duck', 'daffy' => 'duck', 'mickey' => 'mouse'},
        $x ==> $x === 'duck',
        vec['duck', 'duck'],
      ),
      tuple(
        HackLibTestTraversables::getIterator(range(0, 5)),
        $x ==> $x % 2 === 0,
        vec[0, 2, 4],
      ),
    ];
  }

  <<DataProvider('provideTestFilter')>>
  public function testFilter<Tv>(
    Traversable<Tv> $traversable,
    (function(Tv): bool) $value_predicate,
    vec<Tv> $expected,
  ): void {
    expect(Vec\filter($traversable, $value_predicate))->toBeSame($expected);
  }

  public function testFilterWithoutPredicate(): void {
    expect(Vec\filter(varray[
      0, 3, null, 5, false, 40, '', '0', 'win!',
    ]))->toBeSame(vec[3, 5, 40, 'win!']);
  }

  public static function provideTestFilterNulls(): varray<mixed> {
    return varray[
      tuple(
        varray[null, null, null],
        vec[],
      ),
      tuple(
        Map {
          'foo' => false,
          'bar' => null,
          'baz' => '',
          'qux' => 0,
        },
        vec[false, '', 0],
      ),
      tuple(
        Vector {
          'foo',
          'bar',
          null,
          'baz',
        },
        vec['foo', 'bar', 'baz'],
      ),
      tuple(
        HackLibTestTraversables::getKeyedIterator(darray[
          1 => null,
          2 => varray[],
          3 => '0',
        ]),
        vec[varray[], '0'],
      ),
    ];
  }

  <<DataProvider('provideTestFilterNulls')>>
  public function testFilterNulls<Tv>(
  Traversable<?Tv> $traversable,
  vec<Tv> $expected,
  ): void {
    expect(Vec\filter_nulls($traversable))->toBeSame($expected);
  }

  public static function provideTestFilterWithKey(): darray<string, mixed> {
    return darray[
      'All elements selected' => tuple(
        vec['the', 'quick', 'brown', 'fox', 'jumped'],
        ($key, $value) ==> true,
        vec['the', 'quick', 'brown', 'fox', 'jumped'],
      ),
      'No elements selected' => tuple(
        vec['the', 'quick', 'brown', 'fox', 'jumped'],
        ($key, $value) ==> false,
        vec[],
      ),
      'odd elements selected' => tuple(
        vec['the', 'quick', 'brown', 'fox', 'jumped'],
        ($key, $value) ==> $key % 2 === 1,
        vec['quick','fox'],
      ),
      'elements selected starting with "f"' => tuple(
        vec['the', 'quick', 'brown', 'fox', 'jumped'],
        ($key, $value) ==> Str\starts_with($value, 'f'),
        vec['fox'],
      ),
      'elements selected starting with "f" 2' => tuple(
        HackLibTestTraversables::getIterator(
          vec['the', 'quick', 'brown', 'fox', 'jumped']
        ),
        ($key, $value) ==> Str\starts_with($value, 'f'),
        vec['fox'],
      ),
    ];
  }

  <<DataProvider('provideTestFilterWithKey')>>
  public function testFilterWithKey<Tk, Tv>(
    KeyedTraversable<Tk, Tv> $traversable,
    (function(Tk, Tv): bool) $filter_func,
    vec<Tv> $expected,
  ): void {
    $result = Vec\filter_with_key($traversable, $filter_func);
    expect($result)->toBeSame($expected);
  }

  public static function provideTestIntersect(): varray<mixed> {
    return varray[
      tuple(
        range(0, 1000),
        varray[],
        varray[],
        vec[],
      ),
      tuple(
        range(1, 10),
        range(1, 5),
        varray[
          range(2, 6),
          range(3, 7),
        ],
        vec[3, 4, 5],
      ),
      tuple(
        Set {},
        range(1, 100),
        varray[],
        vec[],
      ),
      tuple(
        range(1, 1000),
        Map {},
        varray[
          Set {},
          Vector {},
        ],
        vec[],
      ),
      tuple(
        new Vector(range(1, 100)),
        Map {1 => 2, 39 => 40},
        varray[
          HackLibTestTraversables::getIterator(range(0, 40)),
        ],
        vec[2, 40],
      ),
      tuple(
        varray[3, 4, 4, 5],
        varray[3, 4],
        varray[],
        vec[3, 4, 4],
      ),
    ];
  }

  <<DataProvider('provideTestIntersect')>>
  public function testIntersect<Tv as arraykey>(
    Traversable<Tv> $first,
    Traversable<Tv> $second,
    Container<Traversable<Tv>> $rest,
    vec<Tv> $expected,
  ): void {
    expect(Vec\intersect($first, $second, ...$rest))->toBeSame($expected);
  }

  public static function provideTestKeys(): varray<mixed> {
    return varray[
      tuple(
        darray[
          'foo' => null,
          'bar' => null,
          'baz' => null,
        ],
        vec['foo', 'bar', 'baz'],
      ),
      tuple(
        Map {
          '' => null,
          '0' => null,
          0 => null,
          'foo' => null,
          'false' => null,
        },
        vec['', '0', 0, 'foo', 'false'],
      ),
      tuple(
        Vector {
          'foo',
          'bar',
          null,
          'baz',
        },
        vec[0, 1, 2, 3],
      ),
      tuple(
        HackLibTestTraversables::getKeyedIterator(darray[
          1 => null,
          2 => varray[],
          3 => '0',
        ]),
        vec[1, 2, 3],
      ),
    ];
  }

  <<DataProvider('provideTestKeys')>>
  public function testKeys<Tk, Tv>(
    KeyedTraversable<Tk, Tv> $traversable,
    vec<Tk> $expected,
  ): void {
    expect(Vec\keys($traversable))->toBeSame($expected);
  }

  public static function provideTestSample(): varray<mixed> {
    return varray[
      tuple(
        range(0, 5),
        6,
      ),
      tuple(
        range(0, 5),
        1,
      ),
      tuple(
        range(0, 5),
        10,
      ),
    ];
  }

  <<DataProvider('provideTestSample')>>
  public function testSample<Tv>(
    Traversable<Tv> $traversable,
    int $sample_size,
  ): void {
    $expected_size = Math\minva(C\count(vec($traversable)), $sample_size);
    expect(C\count(Vec\sample($traversable, $sample_size)))
      ->toBeSame($expected_size);
  }

  public function testSampleIterator(): void {
    $iterator = HackLibTestTraversables::getIterator(range(0, 5));
    expect(C\count(Vec\sample($iterator, 3)))->toBeSame(3);
  }

  public static function provideTestSlice(): varray<mixed> {
    return varray[
      tuple(
        range(0, 5),
        6,
        1,
        vec[],
      ),
      tuple(
        new Vector(range(0, 5)),
        2,
        10,
        vec(range(2, 5)),
      ),
      tuple(
        new Set(range(0, 5)),
        2,
        0,
        vec[],
      ),
      tuple(
        new Vector(range(0, 5)),
        2,
        null,
        vec(range(2, 5)),
      ),
    ];
  }

  <<DataProvider('provideTestSlice')>>
  public function testSlice<Tv>(
    Container<Tv> $container,
    int $offset,
    ?int $length,
    vec<Tv> $expected,
  ): void {
    expect(Vec\slice($container, $offset, $length))->toBeSame($expected);
  }

  public static function provideTake(): varray<mixed> {
    return varray[
      tuple(
        vec[],
        5,
        vec[],
      ),
      tuple(
        range(0, 5),
        0,
        vec[],
      ),
      tuple(
        new Vector(range(0, 5)),
        10,
        vec[0, 1, 2, 3, 4, 5],
      ),
      tuple(
        new Set(range(0, 5)),
        2,
        vec[0, 1],
      ),
      tuple(
        HackLibTestTraversables::getIterator(range(0, 5)),
        5,
        vec[0, 1, 2, 3, 4],
      ),
    ];
  }

  <<DataProvider('provideTake')>>
  public function testTake<Tv>(
    Traversable<Tv> $traversable,
    int $n,
    vec<Tv> $expected,
  ): void {
    expect(Vec\take($traversable, $n))->toBeSame($expected);
  }

  public function testTakeIter(): void {
    $iter = HackLibTestTraversables::getIterator(range(0, 4));
    expect(Vec\take($iter, 2))->toBeSame(vec[0, 1]);
    expect(Vec\take($iter, 0))->toBeSame(vec[]);
    expect(Vec\take($iter, 2))->toBeSame(vec[2, 3]);
    expect(Vec\take($iter, 2))->toBeSame(vec[4]);
  }

  public static function provideTestUnique(): varray<mixed> {
    return varray[
      tuple(
        varray['the', 'quick', 'brown', 'fox', 'jumped', 'over', 'the', 'dog'],
        vec['the', 'quick', 'brown', 'fox', 'jumped', 'over', 'dog'],
      ),
      tuple(
        HackLibTestTraversables::getIterator(
          varray[1, 2, 3, 2, 3, 4, 5, 6],
        ),
        vec[1, 2, 3, 4, 5, 6],
      ),
    ];
  }

  <<DataProvider('provideTestUnique')>>
  public function testUnique<Tv as arraykey>(
    Traversable<Tv> $traversable,
    vec<Tv> $expected,
  ): void {
    expect(Vec\unique($traversable))->toBeSame($expected);
  }

  public static function provideTestUniqueBy(): varray<mixed> {
    return varray[
      tuple(
        varray[
          'plum',
          'port',
          'power',
          'push',
          'pin',
          'pygmy',
          'paste',
          'plate',
        ],
        ($str) ==> Str\slice($str, 0, 2),
        vec['plate', 'power', 'push', 'pin', 'pygmy', 'paste'],
      ),
      tuple(
        HackLibTestTraversables::getIterator(
          varray[
            'plum',
            'port',
            'power',
            'push',
            'pin',
            'pygmy',
            'paste',
            'plate',
          ],
        ),
        ($str) ==> Str\slice($str, 0, 2),
        vec['plate', 'power', 'push', 'pin', 'pygmy', 'paste'],
      ),
    ];
  }

  <<DataProvider('provideTestUniqueBy')>>
  public function testUniqueBy<Tv, Ts as arraykey>(
    Traversable<Tv> $traversable,
    (function(Tv): Ts) $scalar_func,
    vec<Tv> $expected,
  ): void {
    expect(Vec\unique_by($traversable, $scalar_func))
      ->toBeSame($expected);
  }

}
