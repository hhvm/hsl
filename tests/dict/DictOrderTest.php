<?hh // strict
/*
 *  Copyright (c) 2004-present, Facebook, Inc.
 *  All rights reserved.
 *
 *  This source code is licensed under the MIT license found in the
 *  LICENSE file in the root directory of this source tree.
 *
 */

use namespace HH\Lib\Dict;
use function Facebook\FBExpect\expect;

/**
 * @emails oncall+hack
 */
final class DictOrderTest extends PHPUnit_Framework_TestCase {

  public static function provideTestReverse(): array<mixed> {
    /* HH_FIXME[2083]  */
    return array(
      tuple(
        Map {},
        dict[],
      ),
      tuple(
        /* HH_FIXME[2083]  */
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
    /* HH_FIXME[2083]  */
    return array(
      tuple(
        Map {
          '0' => 'the',
          '1' => 'quick',
          '2' => 'brown',
          '3' => 'fox',
        },
        null,
        dict[
          '2' => 'brown',
          '3' => 'fox',
          '1' => 'quick',
          '0' => 'the',
        ],
      ),
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
        /* HH_FIXME[2083]  */
        array(
          '0' => 'the',
          1 => 'quick',
          '2' => 'brown',
          3 => 'fox',
        ),
        ($a, $b) ==> $a[1] <=> $b[1],
        dict[
          0 => 'the',
          3 => 'fox',
          2 => 'brown',
          1 => 'quick',
        ],
      ),
      tuple(
        /* HH_FIXME[2083]  */
        HackLibTestTraversables::getKeyedIterator(array(
          'the', 'quick', 'brown', 'fox',
        )),
        ($a, $b) ==> $b[1] <=> $a[1],
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
  public function testSort<Tk as arraykey, Tv>(
    KeyedTraversable<Tk, Tv> $traversable,
    ?(function(Tv, Tv): int) $value_comparator,
    dict<Tk, Tv> $expected,
  ): void {
    expect(Dict\sort($traversable, $value_comparator))->toBeSame($expected);
  }

  public static function provideTestSortBy(): array<mixed> {
    /* HH_FIXME[2083]  */
    return array(
      tuple(
        /* HH_FIXME[2083]  */
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
          /* HH_FIXME[2083]  */
          0 => array('eddard', 'stark'),
          /* HH_FIXME[2083]  */
          1 => array('arya', 'stark'),
          /* HH_FIXME[2083]  */
          2 => array('tyrion', 'lannister'),
          /* HH_FIXME[2083]  */
          3 => array('joffrey', 'boratheon'),
          /* HH_FIXME[2083]  */
          4 => array('daenerys', 'targaryen'),
        },
        fun('array_reverse'),
        ($a, $b) ==> $b <=> $a,
        dict[
          /* HH_FIXME[2083]  */
          4 => array('daenerys', 'targaryen'),
          /* HH_FIXME[2083]  */
          0 => array('eddard', 'stark'),
          /* HH_FIXME[2083]  */
          1 => array('arya', 'stark'),
          /* HH_FIXME[2083]  */
          2 => array('tyrion', 'lannister'),
          /* HH_FIXME[2083]  */
          3 => array('joffrey', 'boratheon'),
        ],
      ),
    );
  }

  /** @dataProvider provideTestSortBy */
  public function testSortBy<Tk as arraykey, Tv, Ts>(
    KeyedTraversable<Tk, Tv> $traversable,
    (function(Tv): Ts) $scalar_func,
    ?(function(Ts, Ts): int) $scalar_comparator,
    dict<Tk, Tv> $expected,
  ): void {
    expect(Dict\sort_by($traversable, $scalar_func, $scalar_comparator))
      ->toBeSame($expected);
  }

  public static function provideTestSortByKey(): array<mixed> {
    /* HH_FIXME[2083]  */
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
        ($a, $b) ==> $b <=> $a,
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
  public function testSortByKey<Tk as arraykey, Tv>(
    KeyedTraversable<Tk, Tv> $traversable,
    ?(function(Tk, Tk): int) $key_comparator,
    dict<Tk, Tv> $expected,
  ): void {
    expect(Dict\sort_by_key($traversable, $key_comparator))
      ->toBeSame($expected);
  }
}
