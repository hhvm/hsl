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
final class DictSelectTest extends PHPUnit_Framework_TestCase {

  public static function provideTestDiffByKey(): array<mixed> {
    return array(
      tuple(
        array(),
        range(0, 100),
        array(),
        dict[],
      ),
      tuple(
        array(1 => 1, 2 => 2, 3 => 3),
        array(),
        array(),
        dict[1 => 1, 2 => 2, 3 => 3],
      ),
      tuple(
        dict['foo' => 'bar', 'baz' => 'qux'],
        Map {'foo' => 4},
        array(),
        dict['baz' => 'qux'],
      ),
      tuple(
        range(0, 9),
        dict[2 => 4, 4 => 8, 8 => 16],
        array(
          Map {1 => 1, 2 => 2},
          HackLibTestTraversables::getKeyedIterator(range(0, 3)),
        ),
        dict[5 => 5, 6 => 6, 7 => 7, 9 => 9],
      ),
    );
  }

  /** @dataProvider provideTestDiffByKey */
  public function testDiffByKey<Tk1, Tk2, Tv>(
    KeyedTraversable<Tk1, Tv> $first,
    KeyedTraversable<Tk2, mixed> $second,
    Container<KeyedTraversable<Tk2, mixed>> $rest,
    dict<Tk1, Tv> $expected,
  ): void {
    expect(DictHSL\diff_by_key($first, $second, ...$rest))->toBeSame($expected);
  }

  public static function provideTestFilter(): array<mixed> {
    return array(
      tuple(
        dict[],
        $x ==> true,
        dict[],
      ),
      tuple(
        dict[],
        $x ==> false,
        dict[],
      ),
      tuple(
        dict[0 => 1],
        $x ==> true,
        dict[0 => 1],
      ),
      tuple(
        dict[0 => 1],
        $x ==> false,
        dict[],
      ),
      tuple(
        dict(range(1, 10)),
        $x ==> $x % 2 === 0,
        dict[1 => 2, 3 => 4, 5 => 6, 7 => 8, 9 => 10],
      ),
      tuple(
        Map {'donald' => 'duck', 'daffy' => 'duck', 'mickey' => 'mouse'},
        $x ==> $x === 'duck',
        dict['donald' => 'duck', 'daffy' => 'duck'],
      ),
      tuple(
        HackLibTestTraversables::getIterator(range(1, 5)),
        $x ==> $x % 2 === 0,
        dict[1 => 2, 3 => 4],
      ),
    );
  }

  /** @dataProvider provideTestFilter */
  public function testFilter<Tk, Tv>(
    KeyedTraversable<Tk, Tv> $traversable,
    (function(Tv): bool) $value_predicate,
    dict<Tk, Tv> $expected,
  ): void {
    expect(DictHSL\filter($traversable, $value_predicate))->toBeSame($expected);
  }

  public static function provideTestFilterWithKey(): array<mixed> {
    return array(
      tuple(
        dict[],
        ($k, $v) ==> true,
        dict[],
      ),
      tuple(
        dict[],
        ($k, $v) ==> false,
        dict[],
      ),
      tuple(
        dict[0 => 1],
        ($k, $v) ==> true,
        dict[0 => 1],
      ),
      tuple(
        dict[0 => 1],
        ($k, $v) ==> false,
        dict[],
      ),
      tuple(
        dict(range(1, 10)),
        ($k, $v) ==> $k % 2 === 0 && $v % 2 === 0,
        dict[],
      ),
      tuple(
        dict(range(1, 10)),
        ($k, $v) ==> $k === $v - 1,
        dict(range(1, 10)),
      ),
      tuple(
        Map {'donald' => 'duck', 'daffy' => 'duck', 'mickey' => 'mouse'},
        ($k, $v) ==> $v === 'duck' && $k === 'donald',
        dict['donald' => 'duck'],
      ),
      tuple(
        HackLibTestTraversables::getIterator(range(1, 5)),
        ($k, $v) ==> $v % 2 === 0,
        dict[1 => 2, 3 => 4],
      ),
    );
  }

  /** @dataProvider provideTestFilterWithKey */
  public function testFilterWithKey<Tk, Tv>(
    KeyedTraversable<Tk, Tv> $traversable,
    (function(Tk, Tv): bool) $predicate,
    dict<Tk, Tv> $expected,
  ): void {
    expect(DictHSL\filter_with_key($traversable, $predicate))->toBeSame($expected);
  }

  public function testFilterWithoutPredicate(): void {
    expect(DictHSL\filter(array(
      0 => 0,
      3 => 3,
      2 => null,
      4 => 5,
      30 => false,
      40 => 40,
      50 => '',
      60 => '0',
      70 => 'win!',
    )))->toBeSame(dict[3 => 3, 4 => 5, 40 => 40, 70 => 'win!']);
  }

  public static function provideTestFilterKeys(): array<mixed> {
    return array(
      tuple(
        dict[],
        $x ==> true,
        dict[],
      ),
      tuple(
        dict[],
        $x ==> false,
        dict[],
      ),
      tuple(
        Map {'donald' => 'duck', 'daffy' => 'duck', 'huey' => 'duck'},
        $x ==> Str\starts_with($x, 'd'),
        dict['donald' => 'duck', 'daffy' => 'duck'],
      ),
      tuple(
        dict(range(1, 10)),
        $x ==> $x % 2 === 0,
        dict[0 => 1, 2 => 3, 4 => 5, 6 => 7, 8 => 9],
      ),
      tuple(
        HackLibTestTraversables::getIterator(range(1, 5)),
        $x ==> $x % 2 === 0,
        dict[0 => 1, 2 => 3, 4 => 5],
      ),
    );
  }

  /** @dataProvider provideTestFilterKeys */
  public function testFilterKeys<Tk, Tv>(
    KeyedTraversable<Tk, Tv> $traversable,
    (function(Tk): bool) $key_predicate,
    dict<Tk, Tv> $expected,
  ): void {
    expect(DictHSL\filter_keys($traversable, $key_predicate))
      ->toBeSame($expected);
  }

  public function testFilterKeysWithoutPredicate(): void {
    expect(DictHSL\filter_keys(dict[
      '' => 1,
      '0' => 2,
      0 => 3,
      1 => 4,
      'hi' => 5,
    ]))->toBeSame(dict[1 => 4, 'hi' => 5]);
  }

  public static function provideTestFilterNulls(): array<mixed> {
    return array(
      tuple(
        array(
          'foo' => null,
          'bar' => null,
          'baz' => null,
        ),
        dict[],
      ),
      tuple(
        Map {
          'foo' => false,
          'bar' => null,
          'baz' => '',
          'qux' => 0,
        },
        dict[
          'foo' => false,
          'baz' => '',
          'qux' => 0,
        ],
      ),
      tuple(
        Vector {
          'foo',
          'bar',
          null,
          'baz',
        },
        dict[
          0 => 'foo',
          1 => 'bar',
          3 => 'baz',
        ],
      ),
      tuple(
        HackLibTestTraversables::getKeyedIterator(array(
          '1' => null,
          '2' => array(),
          '3' => '0',
        )),
        dict[
          2 => array(),
          3 => '0',
        ],
      ),
    );
  }

  /** @dataProvider provideTestFilterNulls */
  public function testFilterNulls<Tk, Tv>(
    KeyedTraversable<Tk, ?Tv> $traversable,
    dict<Tk, Tv> $expected,
  ): void {
    expect(DictHSL\filter_nulls($traversable))->toBeSame($expected);
  }

  public function testGenFilter(): void {
  }

  public static function provideTestSelectKeys(): array<mixed> {
    return array(
      tuple(
        array(),
        array(),
        dict[],
      ),
      tuple(
        Map {
          'foo' => 'foo',
          'bar' => 'bar',
          'baz' => 'baz',
        },
        array('bar'),
        dict[
          'bar' => 'bar',
        ],
      ),
      tuple(
        Vector {'the', 'quick', 'brown', 'fox', 'jumped', 'over', 'the', 'dog'},
        Set {0, 2, 7, 4},
        dict[
          0 => 'the',
          2 => 'brown',
          7 => 'dog',
          4 => 'jumped',
        ],
      ),
    );
  }

  /** @dataProvider provideTestSelectKeys */
  public function testSelectKeys<Tk as arraykey, Tv>(
    KeyedContainer<Tk, Tv> $container,
    Traversable<Tk> $keys,
    dict<Tk, Tv> $expected,
  ): void {
    expect(DictHSL\select_keys($container, $keys))->toBeSame($expected);
  }

  public static function provideTestSlice(): array<mixed> {
    return array(
      tuple(
        Vector {0, 1, 2, 3, 4},
        2,
        0,
        dict[],
      ),
      tuple(
        HackLibTestTraversables::getKeyedIterator(array(
          'foo' => 'oof',
          'bar' => 'rab',
          'baz' => 'zab',
          'qux' => 'xuq',
        )),
        1,
        2,
        dict[
          'bar' => 'rab',
          'baz' => 'zab',
        ],
      ),
      tuple(
        Map {
          'foo' => 'oof',
          'bar' => 'rab',
          'baz' => 'zab',
          'qux' => 'xuq',
          'yap' => 'pay',
        },
        2,
        null,
        dict[
          'baz' => 'zab',
          'qux' => 'xuq',
          'yap' => 'pay',
        ],
      ),
    );
  }

  /** @dataProvider provideTestSlice */
  public function testSlice<Tk, Tv>(
    KeyedTraversable<Tk, Tv> $traversable,
    int $offset,
    ?int $length,
    dict<Tk, Tv> $expected,
  ): void {
    expect(DictHSL\slice($traversable, $offset, $length))->toBeSame($expected);
  }

  public static function provideTestUnique(): array<mixed> {
    return array(
      tuple(
        Map {
          'a' => 1,
          'b' => 2,
          'c' => 2,
          'd' => 1,
          'e' => 3,
        },
        dict[
          'd' => 1,
          'c' => 2,
          'e' => 3,
        ],
      ),
    );
  }

  /** @dataProvider provideTestUnique */
  public function testUnique<Tk as arraykey, Tv as arraykey>(
    KeyedTraversable<Tk, Tv> $traversable,
    dict<Tk, Tv> $expected,
  ): void {
    expect(DictHSL\unique($traversable))->toBeSame($expected);
  }

  public static function provideTestUniqueBy(): array<mixed> {
    $s1 = Set {'foo'};
    $s2 = Set {'bar'};
    $s3 = Set {'foo'};
    $s4 = Set {'baz'};
    return array(
      tuple(
        Vector {$s1, $s2, $s3, $s4},
        ($s) ==> $s->firstKey(),
        dict[
          2 => $s3,
          1 => $s2,
          3 => $s4,
        ],
      ),
    );
  }

  /** @dataProvider provideTestUniqueBy */
  public function testUniqueBy<Tk as arraykey, Tv, Ts as arraykey>(
    KeyedContainer<Tk, Tv> $container,
    (function(Tv): Ts) $scalar_func,
    dict<Tk, Tv> $expected,
  ): void {
    expect(DictHSL\unique_by($container, $scalar_func))->toBeSame($expected);
  }
}
