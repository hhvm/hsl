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

/**
 * @emails oncall+hack
 */
final class KeysetSelectTest extends PHPUnit_Framework_TestCase {

  public static function provideTestDiff(): array<mixed> {
    /* HH_FIXME[2083]  */
    return array(
      tuple(
        /* HH_FIXME[2083]  */
        array(),
        /* HH_FIXME[2083]  */
        array(),
        /* HH_FIXME[2083]  */
        array(),
        keyset[],
      ),
      tuple(
        vec[1, 3, 5, 7],
        dict[],
        /* HH_FIXME[2083]  */
        array(),
        keyset[1, 3, 5, 7],
      ),
      tuple(
        new Vector(range(0, 20)),
        Set {1, 3, 5},
        /* HH_FIXME[2083]  */
        array(
          Map {'foo' => 7, 'bar' => 9},
          HackLibTestTraversables::getIterator(range(11, 30)),
        ),
        keyset[0, 2, 4, 6, 8, 10],
      ),
    );
  }

  /** @dataProvider provideTestDiff */
  public function testDiff<Tv as arraykey>(
    Traversable<Tv> $first,
    Traversable<Tv> $second,
    Container<Traversable<Tv>> $rest,
    keyset<Tv> $expected,
  ): void {
    expect(Keyset\diff($first, $second, ...$rest))
      ->toBeSame($expected);
  }

  public static function provideDrop(): array<mixed> {
    /* HH_FIXME[2083]  */
    return array(
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
        /* HH_FIXME[2083]  */
        HackLibTestTraversables::getIterator(array(0, 1, 2, 3, 4, 5, 5, 5)),
        5,
        keyset[5],
      ),
    );
  }

  /** @dataProvider provideDrop */
  public function testDrop<Tv as arraykey>(
    Traversable<Tv> $traversable,
    int $n,
    keyset<Tv> $expected,
  ): void {
    expect(Keyset\drop($traversable, $n))->toBeSame($expected);
  }

  public static function provideTestFilter(): array<mixed> {
    /* HH_FIXME[2083]  */
    return array(
      tuple(
        /* HH_FIXME[2083]  */
        array(),
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
    );
  }

  /** @dataProvider provideTestFilter */
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
      /* HH_FIXME[2083]  */
      Keyset\filter(array(0, 3, 5, 40, '', '0', 'win!'))
    )->toBeSame(keyset[3, 5, 40, 'win!']);
  }

  public static function provideTestFilterNulls(): array<mixed> {
    /* HH_FIXME[2083]  */
    return array(
      tuple(
        /* HH_FIXME[2083]  */
        array(null, null, null),
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
        /* HH_FIXME[2083]  */
        HackLibTestTraversables::getKeyedIterator(array(
          '1' => null,
          '2' => 1,
          '3' => '0',
        )),
        keyset[1, '0'],
      ),
    );
  }

  /** @dataProvider provideTestFilterNulls */
  public function testFilterNulls<Tv as arraykey>(
    Traversable<?Tv> $traversable,
    keyset<Tv> $expected,
  ): void {
    expect(Keyset\filter_nulls($traversable))->toBeSame($expected);
  }

  public static function provideTestFilterWithKey(): array<string, mixed> {
    /* HH_FIXME[2083]  */
    return array(
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
    );
  }

  /** @dataProvider provideTestFilterWithKey */
  public function testFilterWithKey<Tk, Tv as arraykey>(
    KeyedTraversable<Tk, Tv> $traversable,
    (function(Tk, Tv): bool) $filter_func,
    keyset<Tv> $expected,
  ): void {
    $result = Keyset\filter_with_key($traversable, $filter_func);
    expect($result)->toBeSame($expected);
  }

  public static function provideTestKeys(): array<mixed> {
    /* HH_FIXME[2083]  */
    return array(
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
        /* HH_FIXME[2083]  */
        HackLibTestTraversables::getKeyedIterator(array(
          2 => 4,
          4 => 8,
          6 => 12,
          8 => 16,
        )),
        keyset[2, 4, 6, 8],
      ),
    );
  }

  /** @dataProvider provideTestKeys */
  public function testKeys<Tk as arraykey, Tv>(
    KeyedTraversable<Tk, Tv> $traversable,
    keyset<Tk> $expected,
  ): void {
    expect(Keyset\keys($traversable))->toBeSame($expected);
  }

  public static function provideTestIntersect(): array<mixed> {
    /* HH_FIXME[2083]  */
    return array(
      tuple(
        range(0, 1000),
        /* HH_FIXME[2083]  */
        array(),
        /* HH_FIXME[2083]  */
        array(),
        keyset[],
      ),
      tuple(
        range(1, 10),
        range(1, 5),
        /* HH_FIXME[2083]  */
        array(
          range(2, 6),
          range(3, 7),
        ),
        keyset[3, 4, 5],
      ),
      tuple(
        Set {},
        range(1, 100),
        /* HH_FIXME[2083]  */
        array(),
        keyset[],
      ),
      tuple(
        range(1, 1000),
        Map {},
        /* HH_FIXME[2083]  */
        array(
          Set {},
          Vector {},
        ),
        keyset[],
      ),
      tuple(
        new Vector(range(1, 100)),
        Map {1 => 2, 39 => 40},
        /* HH_FIXME[2083]  */
        array(
          HackLibTestTraversables::getIterator(range(0, 40)),
        ),
        keyset[2, 40],
      ),
      tuple(
        /* HH_FIXME[2083]  */
        array(3, 4, 4, 5),
        /* HH_FIXME[2083]  */
        array(3, 4),
        /* HH_FIXME[2083]  */
        array(),
        keyset[3, 4],
      ),
    );
  }

  /** @dataProvider provideTestIntersect */
  public function testIntersect<Tv as arraykey>(
    Traversable<Tv> $first,
    Traversable<Tv> $second,
    Container<Traversable<Tv>> $rest,
    keyset<Tv> $expected,
  ): void {
    expect(Keyset\intersect($first, $second, ...$rest))
      ->toBeSame($expected);
  }

  public static function provideTake(): array<mixed> {
    /* HH_FIXME[2083]  */
    return array(
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
        /* HH_FIXME[2083]  */
        HackLibTestTraversables::getIterator(array(0, 0, 1, 1, 2, 2, 3, 3)),
        5,
        keyset[0, 1, 2],
      ),
    );
  }

  /** @dataProvider provideTake */
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
