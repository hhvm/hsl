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
use namespace HH\Lib\{C, Math, Vec};

/**
 * Returns the largest of all input numbers.
 *
 * For finding the smallest number, see Math\min.
 * For Traversables, see C\max.
 */
function max<T as num>(T $first_number, T ...$numbers): T {
  $max = $first_number;
  foreach ($numbers as $number) {
    if ($number > $max) {
      $max = $number;
    }
  }
  return $max;
}

/**
 * Returns the arithmetic mean of the numbers in the given container.
 *
 * To find the sum, see `C\sum`.
 * To find the maximum, see `Math\max`.
 * To find the minimum, see `Math\min`.
 */
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
 * To find the mean, see `Math\mean`.
 * To find the standard deviation, see `Math\fb\std_dev`.
 */
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
 * Returns the smallest of all input numbers.
 *
 * For finding the largest number, see Math\max.
 * For Traversables, see C\min.
 */
function min<T as num>(T $first_number, T ...$numbers): T {
  $min = $first_number;
  foreach ($numbers as $number) {
    if ($number < $min) {
      $min = $number;
    }
  }
  return $min;
}
