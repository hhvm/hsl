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

use \HH\Lib\Dict as DictHSL;
use \HH\Lib\Str;
use function \Facebook\FBExpect\expect;

/**
 * @emails oncall+hack_prod_infra
 */
final class DictTransformTest extends PHPUnit_Framework_TestCase {

  public static function provideTestChunk(): array<mixed> {
    return array(
      tuple(
        Map {},
        10,
        vec[],
      ),
      tuple(
        array(0, 1, 2, 3, 4),
        2,
        vec[
          dict[
            0 => 0,
            1 => 1,
          ],
          dict[
            2 => 2,
            3 => 3,
          ],
          dict[
            4 => 4,
          ],
        ],
      ),
      tuple(
        HackLibTestTraversables::getKeyedIterator(
          array('foo' => 'bar', 'baz' => 'qux'),
        ),
        1,
        vec[
          dict['foo' => 'bar'],
          dict['baz' => 'qux'],
        ],
      ),
    );
  }

  /** @dataProvider provideTestChunk */
  public function testChunk<Tk, Tv>(
    KeyedTraversable<Tk, Tv> $traversable,
    int $size,
    vec<dict<Tk, Tv>> $expected,
  ): void {
    expect(DictHSL\chunk($traversable, $size))->toBeSame($expected);
  }

  public static function provideTestCountValues(): array<mixed> {
    return array(
      tuple(
        array(0, '0', 1, 3, 4, 1, 1, 3, '1'),
        dict[
          0 => 1,
          '0' => 1,
          1 => 3,
          3 => 2,
          4 => 1,
          '1' => 1,
        ],
      ),
      tuple(
        Map {
          'donald' => 'duck',
          'bugs' => 'bunny',
          'daffy' => 'duck',
        },
        dict[
          'duck' => 2,
          'bunny' => 1,
        ],
      ),
    );
  }

  /** @dataProvider provideTestCountValues */
  public function testCountValues<Tv as arraykey>(
    Traversable<Tv> $values,
    dict<Tv, int> $expected,
  ): void {
    expect(DictHSL\count_values($values))->toBeSame($expected);
  }

  public static function provideTestFillKeys(): array<mixed> {
    return array(
      tuple(
        array(),
        'foo',
        dict[],
      ),
      tuple(
        Vector {'the', 'quick', 'brown', 'fox'},
        42,
        dict[
          'the' => 42,
          'quick' => 42,
          'brown' => 42,
          'fox' => 42,
        ],
      ),
      tuple(
        HackLibTestTraversables::getIterator(range(1, 3)),
        'hi',
        dict[
          1 => 'hi',
          2 => 'hi',
          3 => 'hi',
        ],
      ),
    );
  }

  /** @dataProvider provideTestFillKeys */
  public function testFillKeys<Tk as arraykey, Tv>(
    Traversable<Tk> $keys,
    Tv $value,
    dict<Tk, Tv> $expected,
  ): void {
    expect(DictHSL\fill_keys($keys, $value))->toBeSame($expected);
  }

  public static function provideTestFlip(): array<mixed> {
    return array(
      tuple(
        array(),
        dict[],
      ),
      tuple(
        Vector {'the', 'quick', 'brown', 'fox'},
        dict[
          'the' => 0,
          'quick' => 1,
          'brown' => 2,
          'fox' => 3,
        ],
      ),
      tuple(
        Map {
          'foo' => 1,
          'bar' => 'bar',
          'baz' => 0,
          'quz' => 'qux',
        },
        dict[
          1 => 'foo',
          'bar' => 'bar',
          0 => 'baz',
          'qux' => 'quz',
        ],
      ),
    );
  }

  /** @dataProvider provideTestFlip */
  public function testFlip<Tk, Tv as arraykey>(
    KeyedTraversable<Tk, Tv> $traversable,
    dict<Tv, Tk> $expected,
  ): void {
    expect(DictHSL\flip($traversable))->toBeSame($expected);
  }

  public static function provideTestFromKeys(): array<mixed> {
    return array(
      tuple(
        Set {},
        $x ==> $x,
        dict[],
      ),
      tuple(
        Map {
          'foo' => 1,
          'bar' => 2,
          'baz' => 3,
        },
        $x ==> $x * $x,
        dict[
          1 => 1,
          2 => 4,
          3 => 9,
        ],
      ),
      tuple(
        array('the', 'quick', 'brown', 'fox'),
        fun('strlen'),
        dict[
          'the' => 3,
          'quick' => 5,
          'brown' => 5,
          'fox' => 3,
        ],
      ),
    );
  }

  /** @dataProvider provideTestFromKeys */
  public function testFromKeys<Tk as arraykey, Tv>(
    Traversable<Tk> $keys,
    (function(Tk): Tv) $value_func,
    dict<Tk, Tv> $expected,
  ): void {
    expect(DictHSL\from_keys($keys, $value_func))->toBeSame($expected);
  }

  public static function provideTestFromEntries(): array<mixed> {
    return array(
      tuple(
        array(
          tuple('foo', 1),
          tuple('bar', null),
          tuple('baz', false),
        ),
        dict[
          'foo' => 1,
          'bar' => null,
          'baz' => false,
        ],
      ),
      tuple(
        Vector {
          tuple('foo', 1),
          tuple('bar', null),
          tuple('baz', false),
        },
        dict[
          'foo' => 1,
          'bar' => null,
          'baz' => false,
        ],
      ),
      tuple(
        HackLibTestTraversables::getIterator(array(
          tuple('foo', 1),
          tuple('bar', null),
          tuple('baz', false),
        )),
        dict[
          'foo' => 1,
          'bar' => null,
          'baz' => false,
        ],
      ),
    );
  }

  /** @dataProvider provideTestFromEntries */
  public function testFromEntries<Tk as arraykey, Tv>(
    Traversable<(Tk, Tv)> $traversable,
    dict<Tk, Tv> $expected,
  ): void {
    expect(DictHSL\from_entries($traversable))->toBeSame($expected);
  }

  public static function provideTestFromValues(): array<mixed> {
    return array(
      tuple(
        array(),
        $x ==> $x,
        dict[],
      ),
      tuple(
        Vector {'the', 'quick', 'brown', 'fox', 'jumped', 'over', 'the', 'dog'},
        fun('strlen'),
        dict[
          3 => 'dog',
          5 => 'brown',
          6 => 'jumped',
          4 => 'over',
        ],
      ),
      tuple(
        Map {
          12 => 'the',
          43 => 'brown',
          'hi' => 'fox',
        },
        $x ==> $x,
        dict[
          'the' => 'the',
          'brown' => 'brown',
          'fox' => 'fox',
        ],
      ),
    );
  }

  /** @dataProvider provideTestFromValues */
  public function testFromValues<Tk as arraykey, Tv>(
    Traversable<Tv> $values,
    (function(Tv): Tk) $key_func,
    dict<Tk, Tv> $expected,
  ): void {
    expect(DictHSL\from_values($values, $key_func))->toBeSame($expected);
  }

  public static function provideTestGroupBy(): array<mixed> {
    return array(
      tuple(
        array('the', 'quick', 'brown', 'fox', 'jumped', 'over', 'the', 'dog'),
        fun('strlen'),
        dict[
          3 => vec['the', 'fox', 'the', 'dog'],
          5 => vec['quick', 'brown'],
          6 => vec['jumped'],
          4 => vec['over'],
        ],
      ),
      tuple(
        HackLibTestTraversables::getIterator(
          array('the', 'quick', 'brown', 'fox', 'jumped', 'over', 'the', 'dog'),
        ),
        $x ==> strlen($x) % 2 === 1 ? strlen($x) : null,
        dict[
          3 => vec['the', 'fox', 'the', 'dog'],
          5 => vec['quick', 'brown'],
        ],
      ),

    );
  }

  /** @dataProvider provideTestGroupBy */
  public function testGroupBy<Tk as arraykey, Tv>(
    Traversable<Tv> $values,
    (function(Tv): ?Tk) $key_func,
    dict<Tk, vec<Tv>> $expected,
  ): void {
    expect(DictHSL\group_by($values, $key_func))->toBeSame($expected);
  }

  public static function provideTestMap(): array<mixed> {

    $doubler = $x ==> $x * 2;
    return array(
      // integer vecs
      tuple(array(), $doubler, dict[]),
      tuple(array(1), $doubler, dict[0 => 2]),
      tuple(range(10, 1000), $doubler, dict(array_map($x ==> $x * 2, range(10, 1000)))),

      // string vecs
      tuple(array('a'), $x ==> $x. ' buzz', dict[0 => 'a buzz']),
      tuple(
        array('a', 'bee', 'a bee'),
        $x ==> $x. ' buzz',
        dict(array('a buzz', 'bee buzz', 'a bee buzz'))
      ),

      // non-vec: Hack Collections and Hack Arrays
      tuple(
        dict(array(
          'donald' => 'duck',
          'daffy' => 'duck',
          'mickey' => 'mouse',
        )),
        $x ==> $x,
        dict['donald' => 'duck', 'daffy' => 'duck', 'mickey' => 'mouse'],
      ),

      tuple(
        Map {'donald' => 'duck', 'daffy' => 'duck', 'mickey' => 'mouse'},
        $x ==> $x,
        dict['donald' => 'duck', 'daffy' => 'duck', 'mickey' => 'mouse'],
      ),

      tuple(
        Vector {10, 20},
        $x ==> $x * 2,
        dict(array(20, 40)),
      ),

      tuple(
        Set {10, 20},
        $x ==> $x * 2,
        dict[10 => 20, 20 => 40],
      ),

      tuple(
        keyset[10, 20],
        $x ==> $x * 2,
        dict[10 => 20, 20 => 40],
      ),

      tuple(
        HackLibTestTraversables::getIterator(array(1, 2, 3)),
        $x ==> $x * 2,
        dict(array(2, 4, 6)),
      ),
      tuple(
        HackLibTestTraversables::getKeyedIterator(array(10 => 1, 20 => 2, 30 => 3)),
        $x ==> $x * 2,
        dict[10 => 2, 20 => 4, 30 => 6],
      ),
    );
  }

  /** @dataProvider provideTestMap */
  public function testMap<Tk, Tv1, Tv2>(
    KeyedTraversable<Tk, Tv1> $traversable,
    (function (Tv1): Tv2) $func,
    dict<Tk, Tv2> $expected,
  ): void {
    expect(DictHSL\map($traversable, $func))->toBeSame($expected);
    if ($traversable instanceof KeyedContainer) {
      // Note: this test might fail because of key-coercion,
      // but at the time of writing none of the cases in the
      // data-provider should experience this coercion.
      expect(DictHSL\map($traversable, $func))->toBeSame(
        dict(array_map($func, $traversable)),
      );
    }
  }

  public static function provideTestMapKeys(): array<mixed> {
    return array(
      tuple(
        dict[
          'the' => 'the',
          'quick' => 'quick',
          'brown' => 'brown',
          'fox' => 'fox',
          'jumps' => 'jumps',
          'over' => 'over',
          'lazy' => 'lazy',
          'dog' => 'dog',
        ],
        fun('strlen'),
        dict[
          3 => 'dog',
          5 => 'jumps',
          4 => 'lazy',
        ],
      ),
      tuple(
        Vector {'the', 'quick', 'brown', 'fox'},
        fun('strval'),
        dict[
          '0' => 'the',
          '1' => 'quick',
          '2' => 'brown',
          '3' => 'fox',
        ],
      ),
      tuple(
        HackLibTestTraversables::getKeyedIterator(array(
          'foo' => 'foo',
          'bar' => 'bar',
          'baz' => 'baz',
        )),
        fun('strrev'),
        dict[
          'oof' => 'foo',
          'rab' => 'bar',
          'zab' => 'baz',
        ],
      ),
    );
  }

  /** @dataProvider provideTestMapKeys */
  public function testMapKeys<Tk1, Tk2 as arraykey, Tv>(
    KeyedTraversable<Tk1, Tv> $traversable,
    (function(Tk1): Tk2) $key_func,
    dict<Tk2, Tv> $expected,
  ): void {
    expect(DictHSL\map_keys($traversable, $key_func))->toBeSame($expected);
  }

  public static function provideTestMapWithKey(): array<mixed> {
    return array(
      tuple(
        array(),
        ($a, $b) ==> null,
        dict[],
      ),
      tuple(
        Vector {'the', 'quick', 'brown', 'fox'},
        ($k, $v) ==> (string)$k.$v,
        dict[
          0 => '0the',
          1 => '1quick',
          2 => '2brown',
          3 => '3fox',
        ],
      ),
      tuple(
        HackLibTestTraversables::getKeyedIterator(range(1, 5)),
        ($k, $v) ==> $k * $v,
        dict[
          0 => 0,
          1 => 2,
          2 => 6,
          3 => 12,
          4 => 20,
        ],
      ),
    );
  }

  /** @dataProvider provideTestMapWithKey */
  public function testMapWithKey<Tk, Tv1, Tv2>(
    KeyedTraversable<Tk, Tv1> $traversable,
    (function(Tk, Tv1): Tv2) $value_func,
    dict<Tk, Tv2> $expected,
  ): void {
    expect(DictHSL\map_with_key($traversable, $value_func))->toBeSame($expected);
  }

  public static function provideTestPull(): array<mixed> {
    return array(
      tuple(
        Vector {'the', 'quick', 'brown', 'fox'},
        $x ==> $x,
        fun('strlen'),
        dict[
          3 => 'fox',
          5 => 'brown',
        ],
      ),
      tuple(
        HackLibTestTraversables::getIterator(
          array(1, 3, 5, 7, 9),
        ),
        ($v) ==> $v * $v,
        fun('strval'),
        dict[
          '1' => 1,
          '3' => 9,
          '5' => 25,
          '7' => 49,
          '9' => 81,
        ],
      ),
    );
  }

  /** @dataProvider provideTestPull */
  public function testPull<Tk as arraykey, Tv1, Tv2>(
    Traversable<Tv1> $traversable,
    (function(Tv1): Tv2) $value_func,
    (function(Tv1): Tk) $key_func,
    dict<Tk, Tv2> $expected,
  ): void {
    expect(DictHSL\pull($traversable, $value_func, $key_func))
      ->toBeSame($expected);
  }

  public static function provideTestPullWithKey(): array<mixed> {
    return array(
      tuple(
        Vector {'the', 'quick', 'brown', 'fox'},
        ($k, $v) ==> $k,
        ($k, $v) ==> Str\slice($v, $k),
        dict[
          'the' => 0,
          'uick' => 1,
          'own' => 2,
          '' => 3,
        ],
      ),
      tuple(
        array(10 => 'foo', 20 => 'food', 30 => 'fool', 40 => 'rude'),
        ($k, $v) ==> $v.(string)$k,
        ($k, $v) ==> Str\slice($v, 0, 3),
        dict[
          'foo' => 'fool30',
          'rud' => 'rude40',
        ],
      ),
    );
  }

  /** @dataProvider provideTestPullWithKey */
  public function testPullWithKey<Tk1, Tk2 as arraykey, Tv1, Tv2>(
    KeyedTraversable<Tk1, Tv1> $traversable,
    (function(Tk1, Tv1): Tv2) $value_func,
    (function(Tk1, Tv1): Tk2) $key_func,
    dict<Tk2, Tv2> $expected,
  ): void {
    expect(DictHSL\pull_with_key($traversable, $value_func, $key_func))
      ->toBeSame($expected);
  }
}
