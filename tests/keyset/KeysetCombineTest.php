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

use namespace HH\Lib\Keyset;
use function Facebook\FBExpect\expect;

/**
 * @emails oncall+hack_prod_infra
 */
final class KeysetCombineTest extends PHPUnit_Framework_TestCase {

  public static function provideTestUnion(): array<mixed> {
    return array(
      tuple(
        array(),
        keyset[],
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

  /** @dataProvider provideTestUnion */
  public function testUnion<Tv as arraykey>(
    Container<Traversable<Tv>> $traversables,
    keyset<Tv> $expected,
  ): void {
    expect(Keyset\union(...$traversables))->toBeSame($expected);
  }
}
