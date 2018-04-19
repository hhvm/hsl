<?hh // strict
/*
 *  Copyright (c) 2004-present, Facebook, Inc.
 *  All rights reserved.
 *
 *  This source code is licensed under the MIT license found in the
 *  LICENSE file in the root directory of this source tree.
 *
 */

/**
 * C is for Containers. This file contains functions that run a calculation
 * over containers and traversables to get a single value result.
 */
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
