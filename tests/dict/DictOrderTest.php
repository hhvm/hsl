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

use namespace HH\Lib\Dict;
use function Facebook\FBExpect\expect;
use function HH\Lib\_Private\mixed_cmp;

/**
 * @emails oncall+hack_prod_infra
 */
final class DictOrderTest extends PHPUnit_Framework_TestCase {

  public static function provideTestReverse(): array<mixed> {
    return array(
      tuple(
        Map {},
        dict[],
      ),
      tuple(
        array(
          'the' => 'quick',
          'brown' => 'fox',
          'jumped' => 'over',
          'a' => 'dog',
        ),
        dict[
          'a' => 'dog',
          'jumped' => 'over',
          'brown' => 'fox',
          'the' => 'quick',
        ],
      ),
      tuple(
        HackLibTestTraversables::getIterator(range(1, 5)),
        dict[
          4 => 5,
          3 => 4,
          2 => 3,
          1 => 2,
          0 => 1,
        ],
      ),
    );
  }

  /** @dataProvider provideTestReverse */
  public function testReverse<Tk as arraykey, Tv>(
    KeyedTraversable<Tk, Tv> $traversable,
    dict<Tk, Tv> $expected,
  ): void {
    expect(Dict\reverse($traversable))->toBeSame($expected);
  }

  public static function provideTestSort(): array<mixed> {
    return array(
      // TODO(#15164841) dict() does integral key coercion.
      // tuple(
      //   Map {
      //     '0' => 'the',
      //     '1' => 'quick',
      //     '2' => 'brown',
      //     '3' => 'fox',
      //   },
      //   null,
      //   dict[
      //     '2' => 'brown',
      //     '3' => 'fox',
      //     '1' => 'quick',
      //     '0' => 'the',
      //   ],
      // ),
      tuple(
        Map {
          'a' => 'the',
          'b' => 'quick',
          'c' => 'brown',
          'd' => 'fox',
        },
        null,
        dict[
          'c' => 'brown',
          'd' => 'fox',
          'b' => 'quick',
          'a' => 'the',
        ],
      ),
      tuple(
        array(
          '0' => 'the',
          1 => 'quick',
          '2' => 'brown',
          3 => 'fox',
        ),
        ($a, $b) ==> mixed_cmp($a[1], $b[1]),
        dict[
          0 => 'the',
          3 => 'fox',
          2 => 'brown',
          1 => 'quick',
        ],
      ),
      tuple(
        HackLibTestTraversables::getKeyedIterator(array(
          'the', 'quick', 'brown', 'fox',
        )),
        ($a, $b) ==> mixed_cmp($b[1], $a[1]),
        dict[
          1 => 'quick',
          2 => 'brown',
          3 => 'fox',
          0 => 'the',
        ],
      ),
    );
  }


  /** @dataProvider provideTestSort */
  public function testSort<Tk, Tv>(
    KeyedTraversable<Tk, Tv> $traversable,
    ?(function(Tv, Tv): int) $value_comparator,
    dict<Tk, Tv> $expected,
  ): void {
    expect(Dict\sort($traversable, $value_comparator))->toBeSame($expected);
  }

  public static function provideTestSortBy(): array<mixed> {
    return array(
      tuple(
        array('the', 'quick', 'brown', 'fox', 'jumped'),
        fun('strrev'),
        null,
        dict[
          4 => 'jumped',
          0 => 'the',
          1 => 'quick',
          2 => 'brown',
          3 => 'fox',
        ],
      ),
      tuple(
        Map {
          0 => array('eddard', 'stark'),
          1 => array('arya', 'stark'),
          2 => array('tyrion', 'lannister'),
          3 => array('joffrey', 'boratheon'),
          4 => array('daenerys', 'targaryen'),
        },
        fun('array_reverse'),
        ($a, $b) ==> mixed_cmp($b, $a),
        dict[
          4 => array('daenerys', 'targaryen'),
          0 => array('eddard', 'stark'),
          1 => array('arya', 'stark'),
          2 => array('tyrion', 'lannister'),
          3 => array('joffrey', 'boratheon'),
        ],
      ),
    );
  }

  /** @dataProvider provideTestSortBy */
  public function testSortBy<Tk, Tv, Ts>(
    KeyedTraversable<Tk, Tv> $traversable,
    (function(Tv): Ts) $scalar_func,
    ?(function(Ts, Ts): int) $scalar_comparator,
    dict<Tk, Tv> $expected,
  ): void {
    expect(Dict\sort_by($traversable, $scalar_func, $scalar_comparator))
      ->toBeSame($expected);
  }

  public static function provideTestSortByKey(): array<mixed> {
    return array(
      tuple(
        Map {
          'the' => 'the',
          'quick' => 'quick',
          'brown' => 'brown',
          'fox' => 'fox',
          'jumped' => 'jumped',
        },
        null,
        dict[
          'brown' => 'brown',
          'fox' => 'fox',
          'jumped' => 'jumped',
          'quick' => 'quick',
          'the' => 'the',
        ],
      ),
      tuple(
        Vector {'the', 'quick', 'brown', 'fox', 'jumped'},
        ($a, $b) ==> mixed_cmp($b, $a),
        dict[
          4 => 'jumped',
          3 => 'fox',
          2 => 'brown',
          1 => 'quick',
          0 => 'the',
        ],
      )
    );
  }

  /** @dataProvider provideTestSortByKey */
  public function testSortByKey<Tk, Tv>(
    KeyedTraversable<Tk, Tv> $traversable,
    ?(function(Tk, Tk): int) $key_comparator,
    dict<Tk, Tv> $expected,
  ): void {
    expect(Dict\sort_by_key($traversable, $key_comparator))
      ->toBeSame($expected);
  }
}
