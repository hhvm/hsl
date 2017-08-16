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

use namespace \HH\Lib\Vec;
use function \Facebook\FBExpect\expect;

/**
 * @emails oncall+hack_prod_infra
 */
final class VecCombineTest extends PHPUnit_Framework_TestCase {

  public static function provideTestConcat(): array<mixed> {
    return array(
      tuple(
        array(),
        vec[],
      ),
      tuple(
        array(
          array(), Vector {}, Map {}, Set {},
        ),
        vec[],
      ),
      tuple(
        array(
          array('the', 'quick'),
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
    Container<Traversable<Tv>> $traversables,
    vec<Tv> $expected,
  ): void {
    expect(Vec\concat(...$traversables))->toBeSame($expected);
  }

}
