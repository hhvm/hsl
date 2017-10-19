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

namespace HH\Lib\Math;

/**
 * Returns the largest of all input numbers.
 *
 * To find the smallest number, see `Math\min`.
 * For Traversables, see `C\max`.
 */
function maxv<T as num>(T $first, T ...$rest): T {
  $max = $first;
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
 * To find the largest number, see `Math\max`.
 * For Traversables, see `C\min`.
 */
function minv<T as num>(T $first, T ...$rest): T {
  $min = $first;
  foreach ($rest as $number) {
    if ($number < $min) {
      $min = $number;
    }
  }
  return $min;
}
