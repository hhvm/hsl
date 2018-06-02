<?hh // strict
/*
 *  Copyright (c) 2004-present, Facebook, Inc.
 *  All rights reserved.
 *
 *  This source code is licensed under the MIT license found in the
 *  LICENSE file in the root directory of this source tree.
 *
 */

use namespace HH\Lib\Vec;
use function Facebook\FBExpect\expect;
// @oss-disable: use InvariantViolationException as InvariantException;

/**
 * @emails oncall+hack
 */
final class VecTransformTest extends PHPUnit_Framework_TestCase {

  public static function provideTestChunk(): array<mixed> {
    /* HH_FIXME[2083]  */
    return array(
      tuple(
        Map {},
        10,
        vec[],
      ),
      tuple(
        /* HH_FIXME[2083]  */
        array(0, 1, 2, 3, 4),
        2,
        vec[
          vec[0, 1],
          vec[2, 3],
          vec[4],
        ],
      ),
      tuple(
        HackLibTestTraversables::getKeyedIterator(
          /* HH_FIXME[2083]  */
          array('foo' => 'bar', 'baz' => 'qux'),
        ),
        1,
        vec[
          vec['bar'],
          vec['qux'],
        ],
      ),
    );
  }

  /** @dataProvider provideTestChunk */
  public function testChunk<Tv>(
    Traversable<Tv> $traversable,
    int $size,
    vec<vec<Tv>> $expected,
  ): void {
    expect(Vec\chunk($traversable, $size))->toBeSame($expected);
  }

  public static function provideTestFill(): array<mixed> {
    /* HH_FIXME[2083]  */
    return array(
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
        /* HH_FIXME[2083]  */
        array('foo' => 'bar'),
        vec[
          /* HH_FIXME[2083]  */
          array('foo' => 'bar'),
          /* HH_FIXME[2083]  */
          array('foo' => 'bar'),
        ],
      ),
    );
  }

  /** @dataProvider provideTestFill */
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

  public static function provideTestFlatten(): array<mixed> {
    /* HH_FIXME[2083]  */
    return array(
      tuple(
        /* HH_FIXME[2083]  */
        array(),
        vec[],
      ),
      tuple(
        /* HH_FIXME[2083]  */
        array(
          /* HH_FIXME[2083]  */
          array(), Vector {}, Map {}, Set {},
        ),
        vec[],
      ),
      tuple(
        /* HH_FIXME[2083]  */
        array(
          /* HH_FIXME[2083]  */
          array('the', 'quick'),
          Vector {'brown', 'fox'},
          Map {'jumped' => 'over'},
          /* HH_FIXME[2083]  */
          HackLibTestTraversables::getIterator(array('the', 'lazy', 'dog')),
        ),
        vec['the', 'quick', 'brown', 'fox', 'over', 'the', 'lazy', 'dog'],
      ),
    );
  }

  /** @dataProvider provideTestFlatten */
  public function testFlatten<Tv>(
    Traversable<Traversable<Tv>> $traversables,
    vec<Tv> $expected,
  ): void {
    expect(Vec\flatten($traversables))->toBeSame($expected);
  }

  public static function provideTestMap(): array<mixed> {
    $doubler = $x ==> $x * 2;
    /* HH_FIXME[2083]  */
    return array(
      tuple(
        /* HH_FIXME[2083]  */
        array(),
        $doubler,
        vec[],
      ),
      tuple(
        /* HH_FIXME[2083]  */
        array(1),
        $doubler,
        vec[2],
      ),
      tuple(
        range(10, 15),
        $doubler,
        vec[20, 22, 24, 26, 28, 30],
      ),
      tuple(
        /* HH_FIXME[2083]  */
        array('a'),
        $x ==> $x. ' buzz',
        vec['a buzz'],
      ),
      tuple(
        /* HH_FIXME[2083]  */
        array('a', 'bee', 'a bee'),
        $x ==> $x. ' buzz',
        vec['a buzz', 'bee buzz', 'a bee buzz'],
      ),
      tuple(
        dict[
          'donald' => 'duck',
          'daffy' => 'duck',
          'mickey' => 'mouse',
        ],
        fun('strrev'),
        vec['kcud', 'kcud', 'esuom'],
      ),
      tuple(
        Map {'donald' => 'duck', 'daffy' => 'duck', 'mickey' => 'mouse'},
        fun('strrev'),
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
        /* HH_FIXME[2083]  */
        HackLibTestTraversables::getIterator(array(1, 2, 3)),
        $doubler,
        vec[2, 4, 6],
      ),
      tuple(
        /* HH_FIXME[2083]  */
        HackLibTestTraversables::getKeyedIterator(array(10 => 1, 20 => 2, 30 => 3)),
        $doubler,
        vec[2, 4, 6],
      ),
    );
  }

  /** @dataProvider provideTestMap */
  public function testMap<Tv1, Tv2>(
    Traversable<Tv1> $traversable,
    (function(Tv1): Tv2) $value_func,
    vec<Tv2> $expected,
  ): void {
    expect(Vec\map($traversable, $value_func))->toBeSame($expected);
  }

  public static function provideTestMapWithKey(): array<mixed> {
    /* HH_FIXME[2083]  */
    return array(
      tuple(
        /* HH_FIXME[2083]  */
        array(),
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
    );
  }

  /** @dataProvider provideTestMapWithKey */
  public function testMapWithKey<Tk, Tv1, Tv2>(
    KeyedTraversable<Tk, Tv1> $traversable,
    (function(Tk, Tv1): Tv2) $value_func,
    vec<Tv2> $expected,
  ): void {
    expect(Vec\map_with_key($traversable, $value_func))->toBeSame($expected);
  }

}
