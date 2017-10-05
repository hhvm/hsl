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

use namespace HH\Lib\Str;
use function Facebook\FBExpect\expect;

/**
 * @emails oncall+hack_prod_infra
 */
final class StrDivideTest extends PHPUnit_Framework_TestCase {

  public static function provideChunk(): array<mixed> {
    return array(
      tuple(
        'hello',
        1,
        vec['h', 'e', 'l', 'l', 'o'],
      ),
      tuple(
        'hello',
        10,
        vec['hello'],
      ),
      tuple(
        'hello',
        2,
        vec['he', 'll', 'o'],
      ),
    );
  }

  /** @dataProvider provideChunk */
  public function testChunk(
    string $string,
    int $chunk_size,
    vec<string> $expected,
  ): void {
    expect(Str\chunk($string, $chunk_size))->toBeSame($expected);
  }

  public static function provideSplit(): array<mixed> {
    return array(
      tuple(
        '',
        '',
        null,
        vec[''],
      ),
      tuple(
        '',
        'hello',
        null,
        vec['h', 'e', 'l', 'l', 'o'],
      ),
      tuple(
        '',
        'hello',
        300,
        vec['h', 'e', 'l', 'l', 'o'],
      ),
      tuple(
        '',
        'hello',
        3,
        vec['h', 'e', 'llo'],
      ),
      tuple(
        '',
        'hello',
        1,
        vec['hello'],
      ),
      tuple(
        '-',
        'hello',
        null,
        vec['hello'],
      ),
      tuple(
        '-',
        '-hello',
        null,
        vec['', 'hello'],
      ),
      tuple(
        '-',
        'hello-',
        null,
        vec['hello', ''],
      ),
      tuple(
        ' ',
        'the quick brown fox jumped',
        null,
        vec['the', 'quick', 'brown', 'fox', 'jumped'],
      ),
      tuple(
        ' ',
        'the quick brown fox jumped',
        3,
        vec['the', 'quick', 'brown fox jumped'],
      ),
    );
  }

  /** @dataProvider provideSplit */
  public function testSplit(
    string $delimiter,
    string $string,
    ?int $limit,
    vec<string> $expected,
  ): void {
    expect(Str\split($string, $delimiter, $limit))
      ->toBeSame($expected);
  }

}
