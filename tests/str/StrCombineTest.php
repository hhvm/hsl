<?hh // strict
/*
 *  Copyright (c) 2004-present, Facebook, Inc.
 *  All rights reserved.
 *
 *  This source code is licensed under the MIT license found in the
 *  LICENSE file in the root directory of this source tree.
 *
 */

use namespace HH\Lib\Str;
use function Facebook\FBExpect\expect;
use type Facebook\HackTest\HackTestCase; // @oss-enable

/**
 * @emails oncall+hack
 */
final class StrCombineTest extends HackTestCase {

  public static function provideJoin(): varray<mixed> {
    $elements = varray['the', 'quick', 'brown', 'fox', 1];
    return varray[
      tuple($elements),
      tuple(new Vector($elements)),
      tuple(new Set($elements)),
      tuple(new Map($elements)),
      tuple(vec($elements)),
      tuple(keyset($elements)),
      tuple(dict($elements)),
      tuple(HackLibTestTraversables::getIterator($elements)),
    ];
  }

  /** @dataProvider provideJoin */
  public function testJoin(
    Traversable<string> $traversable,
  ): void {
    expect(Str\join($traversable, '-'))
      ->toBeSame('the-quick-brown-fox-1');
  }

}
