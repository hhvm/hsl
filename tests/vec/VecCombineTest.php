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

use namespace HH\Lib\Vec;
use function Facebook\FBExpect\expect;

/**
 * @emails oncall+hack
 */
final class VecCombineTest extends PHPUnit_Framework_TestCase {

  public static function provideTestConcat(): array<mixed> {
    return array(
      tuple(
        array(),
        array(),
        vec[],
      ),
      tuple(
        vec[],
        array(
          array(), Vector {}, Map {}, Set {},
        ),
        vec[],
      ),
      tuple(
        array('the', 'quick'),
        array(
          Vector {'brown', 'fox'},
          Map {'jumped' => 'over'},
          HackLibTestTraversables::getIterator(array('the', 'lazy', 'dog')),
        ),
        vec['the', 'quick', 'brown', 'fox', 'over', 'the', 'lazy', 'dog'],
      ),
    );
  }

  /** @dataProvider provideTestConcat */
  public function testConcat<Tv>(
    Traversable<Tv> $first,
    Container<Traversable<Tv>> $rest,
    vec<Tv> $expected,
  ): void {
    expect(Vec\concat($first, ...$rest))->toBeSame($expected);
  }

}
