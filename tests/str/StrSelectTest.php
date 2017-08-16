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
// @oss-disable: use InvariantViolationException as InvariantException;

/**
 * @emails oncall+hack_prod_infra
 */
final class StrSelectTest extends PHPUnit_Framework_TestCase {

  public static function provideSlice(): array<mixed> {
    return array(
      tuple(
        'hello world',
        3,
        3,
        'lo ',
      ),
      tuple(
        'hello world',
        3,
        null,
        'lo world',
      ),
      tuple(
        'hello world',
        3,
        0,
        '',
      ),
      tuple(
        'foo',
        3,
        null,
        '',
      ),
      tuple(
        'foo',
        3,
        12,
        '',
      ),
      tuple(
        'hello world',
        -5,
        null,
        'world',
      ),
      tuple(
        'hello world',
        -5,
        100,
        'world',
      ),
      tuple(
        'hello world',
        -5,
        3,
        'wor',
      ),
    );
  }

  /** @dataProvider provideSlice */
  public function testSlice(
    string $string,
    int $offset,
    ?int $length,
    string $expected,
  ): void {
    expect(Str\slice($string, $offset, $length))->toBeSame($expected);
  }

  public function testSliceExceptions(): void {
    expect(() ==> Str\slice('hello', 0, -1))
      ->toThrow(InvariantException::class);
    expect(() ==> Str\slice('hello', 10))
      ->toThrow(InvariantException::class);
    expect(() ==> Str\slice('hello', -6))
      ->toThrow(InvariantException::class);
  }

  public static function provideStripPrefix(): array<mixed> {
    return array(
      tuple(
        '',
        '',
        '',
      ),
      tuple(
        'hello world',
        '',
        'hello world',
      ),
      tuple(
        'hello world',
        'hello ',
        'world',
      ),
      tuple(
        'world',
        'hello world',
        'world',
      ),
    );
  }

  /** @dataProvider provideStripPrefix */
  public function testStripPrefix(
    string $string,
    string $prefix,
    string $expected,
  ): void {
    expect(Str\strip_prefix($string, $prefix))->toBeSame($expected);
  }

  public static function provideStripSuffix(): array<mixed> {
    return array(
      tuple(
        '',
        '',
        '',
      ),
      tuple(
        'hello world',
        '',
        'hello world',
      ),
      tuple(
        'hello world',
        ' world',
        'hello',
      ),
      tuple(
        'hello',
        'hello world',
        'hello',
      ),
    );
  }

  /** @dataProvider provideStripSuffix */
  public function testStripSuffix(
    string $string,
    string $suffix,
    string $expected,
  ): void {
    expect(Str\strip_suffix($string, $suffix))->toBeSame($expected);
  }

  public static function provideTrim(): array<mixed> {
    return array(
      tuple(
        " \t\n\r\0\x0Bhello \t\n\r\0\x0B world \t\n\r\0\x0B",
        null,
        "hello \t\n\r\0\x0B world",
      ),
      tuple(
        " \t\n\r\0\x0Bhello world \t\n\r\0\x0B",
        'held',
        " \t\n\r\0\x0Bhello world \t\n\r\0\x0B",
      ),
      tuple(
        'hello world',
        'held',
        'o wor',
      ),
    );
  }

  /** @dataProvider provideTrim */
  public function testTrim(
    string $string,
    ?string $char_mask,
    string $expected,
  ): void {
    expect(Str\trim($string, $char_mask))->toBeSame($expected);
  }

  public static function provideTrimLeft(): array<mixed> {
    return array(
      tuple(
        " \t\n\r\0\x0Bhello \t\n\r\0\x0B world \t\n\r\0\x0B",
        null,
        "hello \t\n\r\0\x0B world \t\n\r\0\x0B",
      ),
      tuple(
        " \t\n\r\0\x0Bhello world \t\n\r\0\x0B",
        'held',
        " \t\n\r\0\x0Bhello world \t\n\r\0\x0B",
      ),
      tuple(
        'hello world',
        'held',
        'o world',
      ),
    );
  }

  /** @dataProvider provideTrimLeft */
  public function testTrimLeft(
    string $string,
    ?string $char_mask,
    string $expected,
  ): void {
    expect(Str\trim_left($string, $char_mask))->toBeSame($expected);
  }

  public static function provideTrimRight(): array<mixed> {
    return array(
      tuple(
        " \t\n\r\0\x0Bhello \t\n\r\0\x0B world \t\n\r\0\x0B",
        null,
        " \t\n\r\0\x0Bhello \t\n\r\0\x0B world",
      ),
      tuple(
        " \t\n\r\0\x0Bhello world \t\n\r\0\x0B",
        'held',
        " \t\n\r\0\x0Bhello world \t\n\r\0\x0B",
      ),
      tuple(
        'hello world',
        'held',
        'hello wor',
      ),
    );
  }

  /** @dataProvider provideTrimRight */
  public function testTrimRight(
    string $string,
    ?string $char_mask,
    string $expected,
  ): void {
    expect(Str\trim_right($string, $char_mask))->toBeSame($expected);
  }

}
