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

/**
 * @emails oncall+hack
 */
final class StrIntrospectTest extends PHPUnit_Framework_TestCase {

  public static function provideCompare(): array<mixed> {
    /* HH_FIXME[2083]  */
    return array(
      tuple('foo', 'foo', 0),
      tuple('foo', 'Foo', 1),
      tuple('Foo', 'foo', -1),
      tuple('a', 'b', -1),
      tuple('z', 'a', 1),
    );
  }

  /** @dataProvider provideCompare */
  public function testCompare(
    string $string1,
    string $string2,
    int $expected,
  ): void {
    $actual = Str\compare($string1, $string2);
    if ($expected === 0) {
      expect($actual)->toBeSame(0);
    } else {
      expect($actual * $expected)->toBeGreaterThan(0);
    }
  }

  public static function provideCompareCI(): array<mixed> {
    /* HH_FIXME[2083]  */
    return array(
      tuple('foo', 'foo', 0),
      tuple('foo', 'Foo', 0),
      tuple('Foo', 'foo', 0),
      tuple('a', 'b', -1),
      tuple('z', 'a', 1),
    );
  }

  /** @dataProvider provideCompareCI */
  public function testCompareCI(
    string $string1,
    string $string2,
    int $expected,
  ): void {
    $actual = Str\compare_ci($string1, $string2);
    if ($expected === 0) {
      expect($actual)->toBeSame(0);
    } else {
      expect($actual * $expected)->toBeGreaterThan(0);
    }
  }

  public static function provideContains(): array<mixed> {
    /* HH_FIXME[2083]  */
    return array(
      tuple('', '', 0, true),
      tuple('foo', '', 0, true),
      tuple('foo', '', 3, true),
      tuple('foo', 'o', 3, false),
      tuple('', 'foo', 0, false),
      tuple('fooBar', 'oB', 0, true),
      tuple('fooBar', 'oB', 3, false),
      tuple('foobar', 'oB', 0, false),
      tuple('hello world', 'ow', 0, false),
      tuple('hello world', 'world', -3, false),
      tuple('hello world', 'world', -5, true),
    );
  }

  /** @dataProvider provideContains */
  public function testContains(
    string $haystack,
    string $needle,
    int $offset,
    bool $expected,
  ): void {
    expect(Str\contains($haystack, $needle, $offset))->toBeSame($expected);
  }

  public static function provideContainsCI(): array<mixed> {
    /* HH_FIXME[2083]  */
    return array(
      tuple('', '', 0, true),
      tuple('foo', '', 0, true),
      tuple('foo', '', 3, true),
      tuple('foo', 'o', 3, false),
      tuple('', 'foo', 0, false),
      tuple('fooBar', 'oB', 0, true),
      tuple('fooBar', 'oB', 3, false),
      tuple('foobar', 'BAR', 0, true),
      tuple('hello world', 'ow', 0, false),
      tuple('hello world', 'World', -3, false),
      tuple('hello world', 'World', -5, true),
    );
  }

  /** @dataProvider provideContainsCI */
  public function testContainsCI(
    string $haystack,
    string $needle,
    int $offset,
    bool $expected,
  ): void {
    expect(Str\contains_ci($haystack, $needle, $offset))->toBeSame($expected);
  }

  public function testContainsExceptions(): void {
    expect(() ==> Str\contains('foo', 'f', 5))
      ->toThrow(InvariantException::class);
    expect(() ==> Str\contains('foo', 'f', -1));

    expect(() ==> Str\contains('hello world', 'world', -16))
      ->toThrow(InvariantException::class);
    expect(() ==> Str\contains('hello world', '', -16))
      ->toThrow(InvariantException::class);

    expect(() ==> Str\contains_ci('foo', 'F', 5))
      ->toThrow(InvariantException::class);
    expect(() ==> Str\contains_ci('hello world', 'World', -16))
      ->toThrow(InvariantException::class);
    expect(() ==> Str\contains_ci('hello world', '', -16))
      ->toThrow(InvariantException::class);
  }

  public static function provideEndsWith(): array<mixed> {
    /* HH_FIXME[2083]  */
    return array(
      tuple(
        '',
        '',
        true
      ),
      tuple(
        'hello world',
        '',
        true,
      ),
      tuple(
        'hello world',
        'world',
        true,
      ),
      tuple(
        'world',
        'hello world',
        false,
      ),
      tuple(
        'hello world',
        ' ',
        false,
      ),
      tuple(
        'hello world',
        'WORLD',
        false,
      ),
    );
  }

  /** @dataProvider provideEndsWith */
  public function testEndsWith(
    string $string,
    string $suffix,
    bool $expected,
  ): void {
    expect(Str\ends_with($string, $suffix))->toBeSame($expected);
  }

  public static function provideEndsWithCI(): array<mixed> {
    /* HH_FIXME[2083]  */
    return array(
      tuple(
        '',
        '',
        true
      ),
      tuple(
        'hello world',
        '',
        true,
      ),
      tuple(
        'hello world',
        'world',
        true,
      ),
      tuple(
        'world',
        'hello world',
        false,
      ),
      tuple(
        'hello world',
        ' ',
        false,
      ),
      tuple(
        'hello world',
        'WORLD',
        true,
      ),
    );
  }

  /** @dataProvider provideEndsWithCI */
  public function testEndsWithCI(
    string $string,
    string $suffix,
    bool $expected,
  ): void {
    expect(Str\ends_with_ci($string, $suffix))->toBeSame($expected);
  }

  public static function provideIsEmpty(): array<mixed> {
    /* HH_FIXME[2083]  */
    return array(
      tuple(null, true),
      tuple('', true),
      tuple('0', false),
      tuple("\0", false),
      tuple('anything else', false),
    );
  }

  /** @dataProvider provideIsEmpty */
  public function testIsEmpty(
    ?string $string,
    bool $expected,
  ): void {
    expect(Str\is_empty($string))->toBeSame($expected);
  }

  public static function provideLength(): array<mixed> {
    /* HH_FIXME[2083]  */
    return array(
      tuple('', 0),
      tuple('0', 1),
      tuple('hello', 5),
    );
  }

  /** @dataProvider provideLength */
  public function testLength(
    string $string,
    int $expected,
  ): void {
    expect(Str\length($string))->toBeSame($expected);
  }

  public static function provideSearch(): array<mixed> {
    /* HH_FIXME[2083]  */
    return array(
      tuple('', 'foo', 0, null),
      tuple('fooBar', 'oB', 0, 2),
      tuple('fooBar', 'oB', 3, null),
      tuple('foobar', 'oB', 0, null),
      tuple('foo', 'o', 3, null),
      tuple('hello world', 'ow', 0, null),
      tuple('hello world', 'world', -3, null),
      tuple('hello world', 'world', -5, 6),
    );
  }

  /** @dataProvider provideSearch */
  public function testSearch(
    string $haystack,
    string $needle,
    int $offset,
    ?int $expected,
  ): void {
    expect(Str\search($haystack, $needle, $offset))->toBeSame($expected);
  }

  public static function provideSearchCI(): array<mixed> {
    /* HH_FIXME[2083]  */
    return array(
      tuple('', 'foo', 0, null),
      tuple('fooBar', 'oB', 0, 2),
      tuple('fooBar', 'oB', 3, null),
      tuple('foobar', 'oB', 0, 2),
      tuple('foo', 'o', 3, null),
      tuple('hello world', 'ow', 0, null),
      tuple('hello world', 'World', -3, null),
      tuple('hello world', 'World', -5, 6),
    );
  }

  /** @dataProvider provideSearchCI */
  public function testSearchCI(
    string $haystack,
    string $needle,
    int $offset,
    ?int $expected,
  ): void {
    expect(Str\search_ci($haystack, $needle, $offset))->toBeSame($expected);
  }

  public static function provideSearchLast(): array<mixed> {
    /* HH_FIXME[2083]  */
    return array(
      tuple('foofoofoo', 'foo', 0, 6),
      tuple('foofoofoo', 'bar', 0, null),
      tuple('foobarbar', 'foo', 3, null),
      tuple('foofoofoo', 'Foo', 0, null),
      tuple('foo', 'o', 3, null),
      tuple('foofoofoo', 'foo', -3, 6),
      tuple('foofoofoo', 'foo', -4, 3),
      tuple('hello world', 'world', -3, 6),
      tuple('hello world', 'world', -5, 6),
      tuple('hello world', 'world', -6, null),
    );
  }

  /** @dataProvider provideSearchLast */
  public function testSearchLast(
    string $haystack,
    string $needle,
    int $offset,
    ?int $expected,
  ): void {
    expect(Str\search_last($haystack, $needle, $offset))->toBeSame($expected);
  }

  public function testPositionExceptions(): void {
    expect(() ==> Str\search('foo', 'f', 5))
      ->toThrow(InvariantException::class);
    expect(() ==> Str\search('hello world', 'World', -16))
      ->toThrow(InvariantException::class);

    expect(() ==> Str\search_ci('foo', 'f', 5))
      ->toThrow(InvariantException::class);
    expect(() ==> Str\search_ci('hello world', 'World', -16))
      ->toThrow(InvariantException::class);

    expect(() ==> Str\search_last('foo', 'f', 5))
      ->toThrow(InvariantException::class);
    expect(() ==> Str\search_last('hello world', 'World', -16))
      ->toThrow(InvariantException::class);
  }

  public static function provideStartsWith(): array<mixed> {
    /* HH_FIXME[2083]  */
    return array(
      tuple(
        '',
        '',
        true
      ),
      tuple(
        'hello world',
        '',
        true,
      ),
      tuple(
        'hello world',
        'hello',
        true,
      ),
      tuple(
        'hello',
        'hello world',
        false,
      ),
      tuple(
        'hello world',
        ' ',
        false,
      ),
      tuple(
        'hello world',
        'HELLO',
        false,
      ),
    );
  }

  /** @dataProvider provideStartsWith */
  public function testStartsWith(
    string $string,
    string $prefix,
    bool $expected,
  ): void {
    expect(Str\starts_with($string, $prefix))->toBeSame($expected);
  }

  public static function provideStartsWithCI(): array<mixed> {
    /* HH_FIXME[2083]  */
    return array(
      tuple(
        '',
        '',
        true
      ),
      tuple(
        'hello world',
        '',
        true,
      ),
      tuple(
        'hello world',
        'hello',
        true,
      ),
      tuple(
        'hello',
        'hello world',
        false,
      ),
      tuple(
        'hello world',
        ' ',
        false,
      ),
      tuple(
        'hello world',
        'HELLO',
        true,
      ),
    );
  }

  /** @dataProvider provideStartsWithCI */
  public function testStartsWithCI(
    string $string,
    string $prefix,
    bool $expected,
  ): void {
    expect(Str\starts_with_ci($string, $prefix))->toBeSame($expected);
  }

}
