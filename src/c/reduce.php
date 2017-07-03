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

/** 
 * C is for Containers. This file contains functions that run a calculation
 * over containers and traversables to get a single value result.
 */
/* HH_IGNORE_ERROR[5547] Hack Standard Lib is an exception */
namespace HH\Lib\C;

/**
 * Reduces the given Traversable into a single value by applying an accumulator
 * function against an intermediate result and each value.
 */
function reduce<Tv, Ta>(
  Traversable<Tv> $traversable,
  (function(Ta, Tv): Ta) $accumulator,
  Ta $initial,
): Ta {
  $result = $initial;
  foreach ($traversable as $value) {
    $result = $accumulator($result, $value);
  }
  return $result;
}

/**
 * Returns the intger sum of the values of the given Traversable. An optional
 * function may be provided to convert values to integers, defaulting to casting
 * to int.
 *
 * For a float sum, see C\sum_float.
 */
function sum<T>(
  Traversable<T> $traversable,
  ?(function(T): int) $int_func = null,
): int {
  $int_func = $int_func ?? fun('intval');
  $result = 0;
  foreach ($traversable as $value) {
    $result += $int_func($value);
  }
  return $result;
}

/**
 * Returns the float sum of the values of the given Traversable. An optional
 * function may be provided to convert values to numbers, defaulting to casting
 * to float.
 *
 * For an integer sum, see C\sum.
 */
function sum_float<T>(
  Traversable<T> $traversable,
  ?(function(T): num) $num_func = null,
): float {
  $num_func = $num_func ?? fun('floatval');
  $result = 0.0;
  foreach ($traversable as $value) {
    $result += $num_func($value);
  }
  return $result;
}
