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
use function \Facebook\FBExpect\expect;

/**
 * @emails oncall+hack_prod_infra
 */
final class DictDivideTest extends PHPUnit_Framework_TestCase {

  public static function providePartition(): array<mixed> {
    return array(
      tuple(
        array_combine(range(1, 10), range(11, 20)),
        $val ==> $val % 2 === 0,
        tuple(
          dict[
            2 => 12,
            4 => 14,
            6 => 16,
            8 => 18,
            10 => 20,
          ],
          dict[
            1 => 11,
            3 => 13,
            5 => 15,
            7 => 17,
            9 => 19,
          ],
        ),
      ),
      tuple(
        HackLibTestTraversables::getKeyedIterator(array_combine(range(1, 10), range(11, 20))),
        $val ==> $val % 2 === 0,
        tuple(
          dict[
            2 => 12,
            4 => 14,
            6 => 16,
            8 => 18,
            10 => 20,
          ],
          dict[
            1 => 11,
            3 => 13,
            5 => 15,
            7 => 17,
            9 => 19,
          ],
        ),
      ),
    );
  }

  public static function providePartitionWithKey(): array<mixed> {
    return array(
      tuple(
        array_combine(range(1, 10), range(11, 20)),
        ($key, $val) ==> $val >= 19 || $key <= 3,
        tuple(
          dict[
            1 => 11,
            2 => 12,
            3 => 13,
            9 => 19,
            10 => 20,
          ],
          dict[
            4 => 14,
            5 => 15,
            6 => 16,
            7 => 17,
            8 => 18,
          ],
        ),
      ),
      tuple(
        HackLibTestTraversables::getKeyedIterator(array_combine(range(1, 10), range(11, 20))),
        ($key, $val) ==> $val >= 19 || $key <= 3,
        tuple(
          dict[
            1 => 11,
            2 => 12,
            3 => 13,
            9 => 19,
            10 => 20,
          ],
          dict[
            4 => 14,
            5 => 15,
            6 => 16,
            7 => 17,
            8 => 18,
          ],
        ),
      ),
    );
  }

  /** @dataProvider providePartition */
  public function testPartition<Tk, Tv>(
    KeyedTraversable<Tk, Tv> $traversable,
    (function(Tv): bool) $predicate,
    (dict<Tk, Tv>, dict<Tk, Tv>) $expected,
  ): void {
    expect(DictHSL\partition($traversable, $predicate))->toBeSame($expected);
  }

  /** @dataProvider providePartitionWithKey */
  public function testPartitionWithKey<Tk, Tv>(
    KeyedTraversable<Tk, Tv> $traversable,
    (function(Tk, Tv): bool) $predicate,
    (dict<Tk, Tv>, dict<Tk, Tv>) $expected,
  ): void {
    expect(DictHSL\partition_with_key($traversable, $predicate))->toBeSame($expected);
  }
}
