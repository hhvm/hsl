<?hh // strict
/*
 *  Copyright (c) 2004-present, Facebook, Inc.
 *  All rights reserved.
 *
 *  This source code is licensed under the MIT license found in the
 *  LICENSE file in the root directory of this source tree.
 *
 */

use namespace HH\Lib\Keyset;
use function Facebook\FBExpect\expect;

/**
 * @emails oncall+hack
 */
final class KeysetTransformTest extends PHPUnit_Framework_TestCase {

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
          keyset[0, 1],
          keyset[2, 3],
          keyset[4],
        ],
      ),
      tuple(
        HackLibTestTraversables::getKeyedIterator(
          array('foo' => 'bar', 'baz' => 'qux'),
        ),
        1,
        vec[
          keyset['bar'],
          keyset['qux'],
        ],
      ),
      tuple(
        vec[0, 0, 1, 1, 1, 2, 3, 4, 5, 6],
        3,
        vec[
          keyset[0, 1],
          keyset[1, 2],
          keyset[3, 4, 5],
          keyset[6],
        ],
      ),
    );
  }

  /** @dataProvider provideTestChunk */
  public function testChunk<Tv as arraykey>(
    Traversable<Tv> $traversable,
    int $size,
    vec<keyset<Tv>> $expected,
  ): void {
    expect(Keyset\chunk($traversable, $size))->toBeSame($expected);
  }

  public static function provideTestMap(): array<mixed> {
    $doubler = $x ==> $x * 2;
    return array(
      tuple(
        array(),
        $doubler,
        keyset[],
      ),
      tuple(
        array(1),
        $doubler,
        keyset[2],
      ),
      tuple(
        range(10, 15),
        $doubler,
        keyset[20, 22, 24, 26, 28, 30],
      ),
      tuple(
        array('a'),
        $x ==> $x. ' buzz',
        keyset['a buzz'],
      ),
      tuple(
        array('a', 'bee', 'a bee'),
        $x ==> $x. ' buzz',
        keyset['a buzz', 'bee buzz', 'a bee buzz'],
      ),
      tuple(
        dict[
          'donald' => 'duck',
          'daffy' => 'duck',
          'mickey' => 'mouse',
        ],
        fun('strrev'),
        keyset['kcud', 'kcud', 'esuom'],
      ),
      tuple(
        Map {'donald' => 'duck', 'daffy' => 'duck', 'mickey' => 'mouse'},
        fun('strrev'),
        keyset['kcud', 'kcud', 'esuom'],
      ),
      tuple(
        Vector {10, 20},
        $doubler,
        keyset[20, 40],
      ),
      tuple(
        Set {10, 20},
        $doubler,
        keyset[20, 40],
      ),
      tuple(
        keyset[10, 20],
        $doubler,
        keyset[20, 40],
      ),
      tuple(
        HackLibTestTraversables::getIterator(array(1, 2, 3)),
        $doubler,
        keyset[2, 4, 6],
      ),
      tuple(
        HackLibTestTraversables::getKeyedIterator(array(10 => 1, 20 => 2, 30 => 3)),
        $doubler,
        keyset[2, 4, 6],
      ),
    );
  }

  /** @dataProvider provideTestMap */
  public function testMap<Tv1, Tv2 as arraykey>(
    Traversable<Tv1> $traversable,
    (function(Tv1): Tv2) $value_func,
    keyset<Tv2> $expected,
  ): void {
    expect(Keyset\map($traversable, $value_func))->toBeSame($expected);
  }

  public static function provideTestMapWithKey(): array<mixed> {
    return array(
      tuple(
        array(),
        ($a, $b) ==> null,
        keyset[],
      ),
      tuple(
        Vector {'the', 'quick', 'brown', 'fox'},
        ($k, $v) ==> (string)$k.$v,
        keyset['0the', '1quick', '2brown', '3fox'],
      ),
      tuple(
        HackLibTestTraversables::getKeyedIterator(range(1, 5)),
        ($k, $v) ==> $v * $k,
        keyset[0, 2, 6, 12, 20],
      ),
      tuple(
        range(1, 6),
        ($k, $v) ==> ($k + $v) % 5,
        keyset[1, 3, 0, 2, 4],
      ),
    );
  }

  /** @dataProvider provideTestMapWithKey */
  public function testMapWithKey<Tk, Tv1, Tv2 as arraykey>(
    KeyedTraversable<Tk, Tv1> $traversable,
    (function(Tk, Tv1): Tv2) $value_func,
    keyset<Tv2> $expected,
  ): void {
    expect(Keyset\map_with_key($traversable, $value_func))
      ->toBeSame($expected);
  }

  public static function provideTestFlatten(
  ): array<(Traversable<Traversable<arraykey>>, keyset<arraykey>)> {
    return array(
      tuple(
        vec[keyset[1,2], keyset[2,3,4]],
        keyset[1,2,3,4],
      ),
      tuple(
        vec[keyset[1]],
        keyset[1],
      ),
      tuple(
        vec[],
        keyset[],
      ),
      tuple(
        vec[keyset[], keyset[]],
        keyset[],
      ),
      tuple(
        vec[vec[1,2],vec[2,3]],
        keyset[1,2,3],
      ),
      tuple(
        dict['a' => keyset['apple', 'banana'], 'b' => vec['grape']],
        keyset['apple', 'banana', 'grape'],
      ),
      tuple(
        array(
          array(1, 2, 3),
          Vector {'the', 'quick', 'brown'},
          HackLibTestTraversables::getKeyedIterator(array(
            'the' => 'the',
            'quick' => 'quick',
            'brown' => 'brown',
            'fox' => 'jumped',
          )),
        ),
        keyset[1, 2, 3, 'the', 'quick', 'brown', 'jumped'],
      ),
    );
  }

  /** @dataProvider provideTestFlatten */
  public function testFlatten<Tv as arraykey>(
    Traversable<Traversable<Tv>> $traversables,
    keyset<Tv> $expected,
  ): void {
    expect(Keyset\flatten($traversables))->toBeSame($expected);
  }
}
