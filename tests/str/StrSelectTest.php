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
// @oss-disable: use InvariantViolationException as InvariantException;
use type Facebook\HackTest\DataProvider; // @oss-enable
use type Facebook\HackTest\HackTest as HackTestCase; // @oss-enable

// @oss-disable: <<Oncalls('hack')>>
final class StrSelectTest extends HackTestCase {

  public static function provideSlice(): varray<mixed> {
    return varray[
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
    ];
  }

  <<DataProvider('provideSlice')>>
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

  public static function provideStripPrefix(): varray<mixed> {
    return varray[
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
    ];
  }

  <<DataProvider('provideStripPrefix')>>
  public function testStripPrefix(
    string $string,
    string $prefix,
    string $expected,
  ): void {
    expect(Str\strip_prefix($string, $prefix))->toBeSame($expected);
  }

  public static function provideStripSuffix(): varray<mixed> {
    return varray[
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
    ];
  }

  <<DataProvider('provideStripSuffix')>>
  public function testStripSuffix(
    string $string,
    string $suffix,
    string $expected,
  ): void {
    expect(Str\strip_suffix($string, $suffix))->toBeSame($expected);
  }

  public static function provideTrim(): varray<mixed> {
    return varray[
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
    ];
  }

  <<DataProvider('provideTrim')>>
  public function testTrim(
    string $string,
    ?string $char_mask,
    string $expected,
  ): void {
    expect(Str\trim($string, $char_mask))->toBeSame($expected);
  }

  public static function provideTrimLeft(): varray<mixed> {
    return varray[
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
    ];
  }

  <<DataProvider('provideTrimLeft')>>
  public function testTrimLeft(
    string $string,
    ?string $char_mask,
    string $expected,
  ): void {
    expect(Str\trim_left($string, $char_mask))->toBeSame($expected);
  }

  public static function provideTrimRight(): varray<mixed> {
    return varray[
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
    ];
  }

  <<DataProvider('provideTrimRight')>>
  public function testTrimRight(
    string $string,
    ?string $char_mask,
    string $expected,
  ): void {
    expect(Str\trim_right($string, $char_mask))->toBeSame($expected);
  }

}
