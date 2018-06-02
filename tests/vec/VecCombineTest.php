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

/**
 * @emails oncall+hack
 */
final class VecCombineTest extends PHPUnit_Framework_TestCase {

  public static function provideTestConcat(): array<mixed> {
    /* HH_FIXME[2083]  */
    return array(
      tuple(
        /* HH_FIXME[2083]  */
        array(),
        /* HH_FIXME[2083]  */
        array(),
        vec[],
      ),
      tuple(
        vec[],
        /* HH_FIXME[2083]  */
        array(
          /* HH_FIXME[2083]  */
          array(), Vector {}, Map {}, Set {},
        ),
        vec[],
      ),
      tuple(
        /* HH_FIXME[2083]  */
        array('the', 'quick'),
        /* HH_FIXME[2083]  */
        array(
          Vector {'brown', 'fox'},
          Map {'jumped' => 'over'},
          /* HH_FIXME[2083]  */
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
