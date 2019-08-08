<?hh // strict
/*
 *  Copyright (c) 2004-present, Facebook, Inc.
 *  All rights reserved.
 *
 *  This source code is licensed under the MIT license found in the
 *  LICENSE file in the root directory of this source tree.
 *
 */

namespace HH\Lib\Math;

/**
 * Returns the largest of all input numbers.
 *
 * - To find the smallest number, see `Math\minva()`.
 * - For Traversables, see `Math\max()`.
 */
<<__Rx>>
function maxva<T as num>(
  T $first,
  T $second,
  T ...$rest
): T {
  $max = $first > $second ? $first : $second;
  foreach ($rest as $number) {
    if ($number > $max) {
      $max = $number;
    }
  }
  return $max;
}

/**
 * Returns the smallest of all input numbers.
 *
 * - To find the largest number, see `Math\maxva()`.
 * - For Traversables, see `Math\min()`.
 */
<<__Rx>>
function minva<T as num>(
  T $first,
  T $second,
  T ...$rest
): T {
  $min = $first < $second ? $first : $second;
  foreach ($rest as $number) {
    if ($number < $min) {
      $min = $number;
    }
  }
  return $min;
}

/**
 * Returns whether a num is NAN.
 * NAN is "the not-a-number special float value"
 *
 * When comparing NAN to any value (including NAN) using operators
 * false will be returned. `NAN === NAN` is false.
 *
 * One must always check for NAN using `is_nan` and not `$x === NAN`.
 */
function is_nan(num $num): bool {
  /* HH_IGNORE_ERROR[2049] __PHPStdLib */
  /* HH_IGNORE_ERROR[4107] __PHPStdLib */
  return \is_nan((float)$num);
}
