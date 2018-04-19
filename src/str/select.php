<?hh // strict
/*
 *  Copyright (c) 2004-present, Facebook, Inc.
 *  All rights reserved.
 *
 *  This source code is licensed under the MIT license found in the
 *  LICENSE file in the root directory of this source tree.
 *
 */

namespace HH\Lib\Str;

use namespace HH\Lib\_Private;

/**
 * Returns a substring of length `$length` of the given string starting at the
 * `$offset`.
 *
 * If no length is given, the slice will contain the rest of the
 * string. If the length is zero, the empty string will be returned. If the
 * offset is out-of-bounds, a ViolationException will be thrown.
 *
 * Previously known as `substr` in PHP.
 */
<<__Rx>>
function slice(
  string $string,
  int $offset,
  ?int $length = null,
): string {
  invariant($length === null || $length >= 0, 'Expected non-negative length.');
  $offset = _Private\validate_offset($offset, namespace\length($string));
  $result = $length === null
    ? \substr($string, $offset)
    : \substr($string, $offset, $length);
  if ($result === false) {
    return '';
  }
  return $result;
}

/**
 * Returns the string with the given prefix removed, or the string itself if
 * it doesn't start with the prefix.
 */
<<__Rx>>
function strip_prefix(
  string $string,
  string $prefix,
): string {
  if ($prefix === '' || !namespace\starts_with($string, $prefix)) {
    return $string;
  }
  return namespace\slice($string, namespace\length($prefix));
}

/**
 * Returns the string with the given suffix removed, or the string itself if
 * it doesn't end with the suffix.
 */
<<__Rx>>
function strip_suffix(
  string $string,
  string $suffix,
): string {
  if ($suffix === '' || !namespace\ends_with($string, $suffix)) {
    return $string;
  }
  return namespace\slice(
    $string,
    0,
    namespace\length($string) - namespace\length($suffix),
  );
}

/**
 * Returns the given string with whitespace stripped from the beginning and end.
 *
 * If the optional character mask isn't provided, the following characters will
 * be stripped: space, tab, newline, carriage return, NUL byte, vertical tab.
 *
 * - To only strip from the left, see `Str\trim_left()`.
 * - To only strip from the right, see `Str\trim_right()`.
 */
<<__Rx>>
function trim(
  string $string,
  ?string $char_mask = null,
): string {
  return $char_mask === null
    ? \trim($string)
    : \trim($string, $char_mask);
}

/**
 * Returns the given string with whitespace stripped from the left.
 * See `Str\trim()` for more details.
 *
 * - To strip from both ends, see `Str\trim()`.
 * - To only strip from the right, see `Str\trim_right()`
 */
<<__Rx>>
function trim_left(
  string $string,
  ?string $char_mask = null,
): string {
  return $char_mask === null
    ? \ltrim($string)
    : \ltrim($string, $char_mask);
}

/**
 * Returns the given string with whitespace stripped from the right.
 * See `Str\trim` for more details.
 *
 * - To strip from both ends, see `Str\trim`.
 * - To only strip from the left, see `Str\trim_left`.
 */
<<__Rx>>
function trim_right(
  string $string,
  ?string $char_mask = null,
): string {
  return $char_mask === null
    ? \rtrim($string)
    : \rtrim($string, $char_mask);
}
