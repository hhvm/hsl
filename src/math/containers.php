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
use namespace HH\Lib\{C, Math, Vec};

/**
 * Returns the largest element of the given Traversable, or null if the
 * Traversable is empty.
 *
 * - For a known number of inputs, see `Math\maxva()`.
 * - To find the smallest number, see `Math\min()`.
 */
<<__Rx, __AtMostRxAsArgs>>
function max<T as num>(
  <<__MaybeMutable, __OnlyRxIfImpl(\HH\Rx\Traversable::class)>>
  Traversable<T> $numbers,
): ?T {
  $max = null;
  foreach ($numbers as $number) {
    if ($max === null || $number > $max) {
      $max = $number;
    }
  }
  return $max;
}

/**
 * Returns the largest element of the given Traversable, or null if the
 * Traversable is empty.
 *
 * The value for comparison is determined by the given function. In the case of
 * duplicate numeric keys, later values overwrite previous ones.
 *
 * For numeric elements, see `Math\max()`.
 */
<<__Rx, __AtMostRxAsArgs>>
function max_by<T>(
  <<__MaybeMutable, __OnlyRxIfImpl(\HH\Rx\Traversable::class)>>
  Traversable<T> $traversable,
  <<__AtMostRxAsFunc>>
  (function(T): num) $num_func,
): ?T {
  $max = null;
  $max_num = null;
  foreach ($traversable as $value) {
    $value_num = $num_func($value);
    if ($max_num === null || $value_num >= $max_num) {
      $max = $value;
      $max_num = $value_num;
    }
  }
  return $max;
}

/**
 * Returns the arithmetic mean of the numbers in the given container.
 *
 * - To find the sum, see `Math\sum()`.
 * - To find the maximum, see `Math\max()`.
 * - To find the minimum, see `Math\min()`.
 */
<<__Rx>>
function mean(Container<num> $numbers): ?float {
  $count = (float)C\count($numbers);
  if ($count === 0.0) {
    return null;
  }
  $mean = 0.0;
  foreach ($numbers as $number) {
    $mean += $number / $count;
  }
  return $mean;
}

/**
 * Returns the median of the given numbers.
 *
 * To find the mean, see `Math\mean()`.
 */
<<__Rx>>
function median(Container<num> $numbers): ?float {
  $numbers = Vec\sort($numbers);
  $count = C\count($numbers);
  if ($count === 0) {
    return null;
  }
  $middle_index = Math\int_div($count, 2);
  if ($count % 2 === 0) {
    return Math\mean(
      vec[$numbers[$middle_index], $numbers[$middle_index - 1]]
    ) ?? 0.0;
  }
  return (float)$numbers[$middle_index];
}

/**
 * Returns the smallest element of the given Traversable, or null if the
 * Traversable is empty.
 *
 * - For a known number of inputs, see `Math\minva()`.
 * - To find the largest number, see `Math\max()`.
 */
<<__Rx, __AtMostRxAsArgs>>
function min<T as num>(
  <<__MaybeMutable, __OnlyRxIfImpl(\HH\Rx\Traversable::class)>>
  Traversable<T> $numbers,
): ?T {
  $min = null;
  foreach ($numbers as $number) {
    if ($min === null || $number < $min) {
      $min = $number;
    }
  }
  return $min;
}

/**
 * Returns the smallest element of the given Traversable, or null if the
 * Traversable is empty.
 *
 * The value for comparison is determined by the given function. In the case of
 * duplicate numeric keys, later values overwrite previous ones.
 *
 * For numeric elements, see `Math\min()`.
 */
<<__Rx, __AtMostRxAsArgs>>
function min_by<T>(
  <<__MaybeMutable, __OnlyRxIfImpl(\HH\Rx\Traversable::class)>>
  Traversable<T> $traversable,
  <<__AtMostRxAsFunc>>
  (function(T): num) $num_func,
): ?T {
  $min = null;
  $min_num = null;
  foreach ($traversable as $value) {
    $value_num = $num_func($value);
    if ($min_num === null || $value_num <= $min_num) {
      $min = $value;
      $min_num = $value_num;
    }
  }
  return $min;
}

/**
 * Returns the integer sum of the values of the given Traversable.
 *
 * For a float sum, see `Math\sum_float()`.
 */
<<__Rx, __AtMostRxAsArgs>>
function sum(
  <<__MaybeMutable, __OnlyRxIfImpl(\HH\Rx\Traversable::class)>>
  Traversable<int> $traversable,
): int {
  $result = 0;
  foreach ($traversable as $value) {
    $result += $value;
  }
  return $result;
}

/**
 * Returns the float sum of the values of the given Traversable.
 *
 * For an integer sum, see `Math\sum()`.
 */
<<__Rx, __AtMostRxAsArgs>>
function sum_float<T as num>(
  <<__MaybeMutable, __OnlyRxIfImpl(\HH\Rx\Traversable::class)>>
  Traversable<T> $traversable,
): float {
  $result = 0.0;
  foreach ($traversable as $value) {
    $result += $value;
  }
  return $result;
}
