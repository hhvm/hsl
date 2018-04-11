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
final class KeysetCombineTest extends PHPUnit_Framework_TestCase {

  public static function provideTestUnion(): array<mixed> {
    return array(
      tuple(
        array(),
        array(
          vec[],
        ),
        keyset[],
      ),
      tuple(
        keyset[1, 2, 3],
        array(
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
    Traversable<Tv> $first,
    Container<Traversable<Tv>> $rest,
    keyset<Tv> $expected,
  ): void {
    expect(Keyset\union($first, ...$rest))->toBeSame($expected);
  }
}
