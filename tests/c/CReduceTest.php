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

use namespace \HH\Lib\C;
use function \Facebook\FBExpect\expect;

/**
 * @emails oncall+hack_prod_infra
 */
final class CReduceTest extends PHPUnit_Framework_TestCase {

  public static function provideTestReduce(): array<mixed> {
    return array(
      tuple(
        Set {'the', ' quick', ' brown', ' fox'},
        ($a, $s) ==> $a.$s,
        '',
        'the quick brown fox',
      ),
      tuple(
        HackLibTestTraversables::getIterator(
          array('the', ' quick', ' brown', ' fox'),
        ),
        ($a, $s) ==> $a.$s,
        '',
        'the quick brown fox',
      ),
      tuple(
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

  public static function provideTestSum(): array<mixed> {
    return array(
      tuple(
        vec['the', 'quick', 'brown', 'fox'],
        fun('strlen'),
        16,
      ),
      tuple(
        HackLibTestTraversables::getIterator(
          array('the', 'quick', 'brown', 'fox'),
        ),
        fun('strlen'),
        16,
      ),
    );
  }

  /** @dataProvider provideTestSum */
  public function testSum<T>(
    Traversable<T> $traversable,
    (function(T): int) $int_func,
    int $expected,
  ): void {
    expect(C\sum($traversable, $int_func))->toBeSame($expected);

  }

  public static function provideTestSumWithoutIntFunc(): array<mixed> {
    return array(
      tuple(
        Vector {},
        0,
      ),
      tuple(
        array(1, 2, 1, 1, 3),
        8,
      ),
      tuple(
        HackLibTestTraversables::getIterator(range(1, 4)),
        10,
      ),
    );
  }

  /** @dataProvider provideTestSumWithoutIntFunc */
  public function testSumWithoutIntFunc<T>(
    Traversable<T> $traversable,
    int $expected,
  ): void {
    expect(C\sum($traversable))->toBeSame($expected);
  }

  public static function provideTestSumFloat(): array<mixed> {
    return array(
      tuple(
        vec['the', 'quick', 'brown', 'fox', 'jumped', 'over', 'the'],
        ($s) ==> strlen($s) / 2,
        14.5,
      ),
      tuple(
        HackLibTestTraversables::getIterator(
          array('the', 'quick', 'brown', 'fox', 'jumped', 'over', 'the'),
        ),
        ($s) ==> strlen($s) / 2,
        14.5,
      ),
    );
  }

  /** @dataProvider provideTestSumFloat */
  public function testSumFloat<T>(
    Traversable<T> $traversable,
    (function(T): num) $num_func,
    float $expected,
  ): void {
    expect(C\sum_float($traversable, $num_func))->toBeSame($expected);
  }

  public static function provideTestSumFloatWithoutNumFunc(): array<mixed> {
    return array(
      tuple(
        Vector {},
        0.0,
      ),
      tuple(
        array(1, 2.5, 1, 1, 3),
        8.5,
      ),
      tuple(
        HackLibTestTraversables::getIterator(range(1, 4)),
        10.0,
      ),
    );
  }

  /** @dataProvider provideTestSumFloatWithoutNumFunc */
  public function testSumFloatWithoutNumFunc<T>(
    Traversable<T> $traversable,
    float $expected,
  ): void {
    expect(C\sum_float($traversable))->toBeSame($expected);
  }
}
