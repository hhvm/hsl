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

namespace HH\Lib\Str;

use namespace HH\Lib\_Private;

/**
 * Returns the string with the first character capitalized.
 *
 * If the first character is already capitalized or isn't alphabetic, the string
 * will be unchanged.
 *
 * - To capitalize all characters, see `Str\uppercase()`.
 * - To capitalize all words, see `Str\capitalize_words()`.
 */
function capitalize(
  string $string,
): string {
  return \ucfirst($string);
}

/**
 * Returns the string with all words capitalized.
 *
 * Words are delimited by space, tab, newline, carriage return, form-feed, and
 * vertical tab by default, but you can specify custom delimiters.
 *
 * - To capitalize all characters, see `Str\uppercase()`.
 * - To capitalize only the first character, see `Str\capitalize()`.
 */
function capitalize_words(
  string $string,
  string $delimiters = " \t\r\n\f\v",
): string {
  return \ucwords($string, $delimiters);
}

/**
 * Returns a string representation of the given number with grouped thousands.
 *
 * If `$decimals` is provided, the string will contain that many decimal places.
 * The optional `$decimal_point` and `$thousands_separator` arguments define the
 * strings used for decimals and commas, respectively.
 */
function format_number(
  num $number,
  int $decimals = 0,
  string $decimal_point = '.',
  string $thousands_separator = ',',
): string {
  return \number_format(
    $number,
    $decimals,
    $decimal_point,
    $thousands_separator,
  );
}

/**
 * Returns the string with all alphabetic characters converted to lowercase.
 */
function lowercase(
  string $string,
): string {
  return \strtolower($string);
}

/**
 * Returns the string padded to the total length by appending the `$pad_string`
 * to the left.
 *
 * If the length of the input string plus the pad string exceeds the total
 * length, the pad string will be truncated. If the total length is less than or
 * equal to the length of the input string, no padding will occur.
 *
 * To pad the string on the right, see `Str\pad_right()`.
 */
function pad_left(
  string $string,
  int $total_length,
  string $pad_string = ' ',
): string {
  invariant($pad_string !== '', 'Expected non-empty pad string.');
  invariant($total_length >= 0, 'Expected non-negative total length.');
  return \str_pad($string, $total_length, $pad_string, \STR_PAD_LEFT);
}

/**
 * Returns the string padded to the total length by appending the `$pad_string`
 * to the right.
 *
 * If the length of the input string plus the pad string exceeds the total
 * length, the pad string will be truncated. If the total length is less than or
 * equal to the length of the input string, no padding will occur.
 *
 * To pad the string on the left, see `Str\pad_left()`.
 */
function pad_right(
  string $string,
  int $total_length,
  string $pad_string = ' ',
): string {
  invariant($pad_string !== '', 'Expected non-empty pad string.');
  invariant($total_length >= 0, 'Expected non-negative total length.');
  return \str_pad($string, $total_length, $pad_string, \STR_PAD_RIGHT);
}

/**
 * Returns the input string repeated `$multiplier` times.
 *
 * If the multiplier is 0, the empty string will be returned.
 */
function repeat(
  string $string,
  int $multiplier,
): string {
  invariant($multiplier >= 0, 'Expected non-negative multiplier');
  return \str_repeat($string, $multiplier);
}

/**
 * Returns the "haystack" string with all occurences of `$needle` replaced by
 * `$replacement`.
 *
 * - For a case-insensitive search/replace, see `Str\replace_ci()`.
 * - For multiple searches/replacements, see `Str\replace_every()`.
 */
function replace(
  string $haystack,
  string $needle,
  string $replacement,
): string {
  return \str_replace($needle, $replacement, $haystack);
}

/**
 * Returns the "haystack" string with all occurences of `$needle` replaced by
 * `$replacement` (case-insensitive).
 *
 * - For a case-sensitive search/replace, see `Str\replace()`.
 * - For multiple searches/replacements, see `Str\replace_every()`.
 */
function replace_ci(
  string $haystack,
  string $needle,
  string $replacement,
): string {
  return \str_ireplace($needle, $replacement, $haystack);
}

/**
 * Returns the "haystack" string with all occurences of the keys of
 * `$replacements` replaced by the corresponding values.
 *
 * For a single search/replace, see `Str\replace()`.
 */
function replace_every(
  string $haystack,
  KeyedContainer<string, string> $replacements,
): string {
  return \str_replace(
    \array_keys($replacements),
    \array_values($replacements),
    $haystack,
  );
}

/**
 * Return the string with a slice specified by the offset/length replaced by the
 * given replacement string.
 *
 * If the length is omitted or exceeds the upper bound of the string, the
 * remainder of the string will be replaced. If the length is zero, the
 * replacement will be inserted at the offset.
 *
 * Previously known in PHP as `substr_replace`.
 */
function splice(
  string $string,
  string $replacement,
  int $offset,
  ?int $length = null,
): string {
  invariant($length === null || $length >= 0, 'Expected non-negative length.');
  $offset = _Private\validate_offset($offset, namespace\length($string));
  return $length === null
    ? \substr_replace($string, $replacement, $offset)
    : \substr_replace($string, $replacement, $offset, $length);
}

/**
 * Returns the given string as an integer, or null if the string isn't numeric.
 */
function to_int(
  string $string,
): ?int {
  if ((string)(int)$string === $string) {
    return (int)$string;
  }
  return null;
}

/**
 * Returns the string with all alphabetic characters converted to uppercase.
 */
function uppercase(
  string $string,
): string {
  return \strtoupper($string);
}
