<?hh // strict
/*
 *  Copyright (c) 2004-present, Facebook, Inc.
 *  All rights reserved.
 *
 *  This source code is licensed under the MIT license found in the
 *  LICENSE file in the root directory of this source tree.
 *
 */

use namespace HH\Lib\C;
use function Facebook\FBExpect\expect;

/**
 * @emails oncall+hack
 */
final class CReduceTest extends PHPUnit_Framework_TestCase {

  public static function provideTestReduce(): array<mixed> {
    /* HH_FIXME[2083]  */
    return array(
      tuple(
        Set {'the', ' quick', ' brown', ' fox'},
        ($a, $s) ==> $a.$s,
        '',
        'the quick brown fox',
      ),
      tuple(
        HackLibTestTraversables::getIterator(
          /* HH_FIXME[2083]  */
          array('the', ' quick', ' brown', ' fox'),
        ),
        ($a, $s) ==> $a.$s,
        '',
        'the quick brown fox',
      ),
      tuple(
        /* HH_FIXME[2083]  */
        array('the', 'quick', 'brown', 'fox'),
        ($a, $s) ==> $a->add($s),
        Vector {},
        Vector {'the', 'quick', 'brown', 'fox'},
      ),
    );
  }

  /** @dataProvider provideTestReduce */
  public function testReduce<Tv, Ta>(
    Traversable<Tv> $traversable,
    (function(Ta, Tv): Ta) $accumulator,
    Ta $initial,
    Ta $expected,
  ): void {
    expect(C\reduce($traversable, $accumulator, $initial))->toEqual($expected);
  }

  public static function provideTestReduceWithKey(): array<mixed> {
    /* HH_FIXME[2083]  */
    return array(
      tuple(
        'dict',
        dict[1 => 2, 2 => 3, 3 => 4, 4 => 5],
        dict[1 => 0, 4 => 6, 8 => 10],
        dict[1 => 0],
      ),
      tuple(
        'array',
        /* HH_FIXME[2083]  */
        array(1 => 2, 2 => 3, 3 => 4, 4 => 5),
        dict[1 => 0, 4 => 6, 8 => 10],
        dict[1 => 0],
      ),
      tuple(
        'map',
        Map{1 => 2, 2 => 3, 3 => 4, 4 => 5},
        dict[1 => 0, 4 => 6, 8 => 10],
        dict[1 => 0],
      ),
      tuple(
        'generator',
        HackLibTestTraversables::getKeyedIterator(
          dict[1 => 2, 2 => 3, 3 => 4, 4 => 5],
        ),
        dict[1 => 0, 4 => 6, 8 => 10],
        dict[1 => 0],
      ),
    );
  }

  /** @dataProvider provideTestReduceWithKey */
  public function testReduceWithKey(
    string $_,
    KeyedTraversable<int, int> $traversable,
    dict<int, int> $expected,
    dict<int, int> $initial,
  ): void {
    $result = C\reduce_with_key(
      $traversable,
      ($acc, $k, $v) ==> {
        if ($k %2  === 0) {
            $acc[$k * 2] = $v * 2;
        }
        return $acc;
      },
      $initial,
    );

    expect($result)->toBeSame($expected);
  }
}
