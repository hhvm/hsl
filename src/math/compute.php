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

/* HH_IGNORE_ERROR[5547] Hack Standard Lib is an exception */
namespace HH\Lib\Math;
use HH\Lib\Str;
use const HH\Lib\_Private\ALPHABET_ALPHANUMERIC;

/**
 * Returns the absolute value of `$number` (`$number` if `$number` > 0,
 * `-$number` if `$number` < 0).
 */
function abs<T as num>(T $number): T {
  return $number < 0 ? -$number : $number;
}

/**
 * Converts the given string in base `$from_base` to base `$to_base`, assuming
 * letters a-z are used for digits for bases greater than 10. The conversion is
 * done to arbitrary precision.
 *
 * To convert a string in some base to an int, see `Math\from_base`.
 * To convert an int to a string in some base, see `Math\to_base`.
 */
function base_convert(string $value, int $from_base, int $to_base): string {
  invariant(
    $value !== '',
    'Unexpected empty string, expected number in base %d',
    $from_base,
  );

  invariant(
    $from_base >= 2 && $from_base <= 36,
    'Expected $from_base to be between 2 and 36, got %d',
    $from_base,
  );

  invariant(
    $to_base >= 2 && $to_base <= 36,
    'Expected $to_base to be between 2 and 36, got %d',
    $to_base,
  );

  invariant(\bcscale(0) === true, 'Unexpected bcscale failure');

  $from_alphabet = Str\slice(ALPHABET_ALPHANUMERIC, 0, $from_base);
  $result_decimal = '0';
  $place_value = \bcpow((string)$from_base, (string)(Str\length($value) - 1));
  foreach (Str\chunk($value) as $digit) {
    $digit_numeric = Str\search_ci($from_alphabet, $digit);
    invariant(
      $digit_numeric !== null,
      'Invalid digit %s in base %d',
      $digit,
      $from_base,
    );
    $result_decimal = \bcadd(
      $result_decimal,
      \bcmul((string)$digit_numeric, $place_value),
    );
    $place_value = \bcdiv((string)$place_value, (string)$from_base);
  }

  if ($to_base === 10) {
    return $result_decimal;
  }

  $to_alphabet = Str\slice(ALPHABET_ALPHANUMERIC, 0, $to_base);
  $result = '';
  do {
    $result = $to_alphabet[\bcmod($result_decimal, (string)$to_base)] . $result;
    $result_decimal = \bcdiv((string)$result_decimal, (string)$to_base);
  } while (\bccomp($result_decimal, '0') > 0);

  return $result;
}

/**
 * Returns the smallest integer value greater than or equal to $value.
 *
 * To find the largest integer value less than or equal to `$value`, see
 * `Math\floor`.
 */
function ceil(num $value): float {
  return \ceil($value);
}

/**
 * Returns the cosine of `$arg`.
 *
 * To find the sine, see `Math\sin`.
 * To find the tangent, see `Math\tan`.
 */
function cos(num $arg): float {
  return \cos($arg);
}

/**
 * Converts the given string in the given base to an int, assuming letters a-z
 * are used for digits when `$from_base` > 10.
 *
 * To base convert an int into a string, see `Math\to_base`.
 */
function from_base(string $number, int $from_base): int {
  $result_string = base_convert($number, $from_base, 10);
  $result = Str\to_int($result_string);
  invariant(
    $result !== null,
    'Unexpected integer overflow parsing %s from base %d',
    $number,
    $from_base,
  );
  return $result;
}

/**
 * Returns e to the power `$arg`.
 *
 * To find the logarithm, see `Math\log`.
 */
function exp(num $arg): float {
  return \exp($arg);
}

/**
 * Returns the largest integer value less than or equal to `$value`.
 *
 * To find the smallest integer value greater than or equal to `$value`, see
 * `Math\ceil`.
 * To find the largest integer value less than or equal to a ratio, see
 * `Math\int_div`.
 */
function floor(num $value): float {
  return \floor($value);
}

/**
 * Returns the result of integer division of `$numerator` by `$denominator`.
 *
 * To round a single value, see `Math\floor`.
 */
function int_div(int $numerator, int $denominator): int {
  if ($denominator === 0) {
    throw new DivisionByZeroException();
  }
  return \intdiv($numerator, $denominator);
}

/**
 * Returns the logarithm base `$base` of `$arg`.
 *
 * For the exponential function, see `Math\exp`.
 */
function log(num $arg, num $base = \M_E): float {
  invariant($arg > 0, 'Expected positive argument for log, got %f', $arg);
  invariant($base > 0, 'Expected positive base for log, got %f', $base);
  invariant($base !== 1, 'Logarithm undefined for base 1');
  return \log($arg, $base);
}

/**
 * Returns the arithmetic mean of the given numbers.
 *
 * To find the sum, see `C\sum`.
 * To find the maximum, see `Math\max`.
 * To find the minimum, see `Math\min`.
 */
function mean(num $first_number, num ...$numbers): float {
  $count = (float)\HH\Lib\C\count($numbers) + 1;
  $mean = $first_number / $count;
  foreach ($numbers as $number) {
    $mean += $number / $count;
  }
  return $mean;
}

/**
 * Returns the given number rounded to the specified precision. A positive
 * precision rounds to the nearest decimal place whereas a negative precision
 * rounds to the nearest power of ten. For example, a precision of -1 rounds to
 * the nearest tenth whereas a precision of 1 rounds to the nearest ten.
 */
function round(
  num $val,
  int $precision = 0,
): float {
  return \round($val, $precision);
}

/**
 * Returns the sine of $arg.
 *
 * To find the cosine, see `Math\cos`.
 * To find the tangent, see `Math\tan`.
 */
function sin(num $arg): float {
  return \sin($arg);
}

/**
 * Returns the square root of `$arg`.
 */
function sqrt(num $arg): float {
  invariant($arg >= 0, 'Expected non-negative argument to sqrt, got %f', $arg);
  return \sqrt($arg);
}

/**
 * Returns the tangent of `$arg`.
 *
 * To find the cosine, see `Math\cos`.
 * To find the sine, see `Math\sin`.
 */
function tan(num $arg): float {
  return \tan($arg);
}

/**
 * Converts the given non-negative number into the given base, using letters a-z
 * for digits when `$to_base` > 10.
 *
 * To base convert a string to an int, see `Math\from_base`.
 */
function to_base(int $number, int $to_base): string {
  invariant(
    $to_base >= 2 && $to_base <= 36,
    'Expected $to_base to be between 2 and 36, got %d',
    $to_base,
  );
  invariant(
    $number >= 0,
    'Expected non-negative base conversion input, got %d',
    $number,
  );
  return base_convert((string)$number, 10, $to_base);
}
