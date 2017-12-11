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

namespace HH\Lib\Vec;

use namespace HH\Lib\{C, Dict, Math, Str};

/**
 * Returns a new vec containing the range of numbers from `$start` to `$end`
 * inclusive, with the step between elements being `$step` if provided, or 1 by
 * default. If `$start > $end`, it returns a descending range instead of
 * an empty one.
 */
function range<Tv as num>(
  Tv $start,
  Tv $end,
  ?Tv $step = null,
): vec<Tv> {
  $step = $step ?? 1;
  invariant($step > 0, 'Expected positive step.');
  if ($step > Math\abs($end - $start)) {
    return vec[$start];
  }
  return vec(\range($start, $end, $step));
}

/**
 * Returns a new vec with the values of the given Traversable in reversed
 * order.
 */
function reverse<Tv>(
  Traversable<Tv> $traversable,
): vec<Tv> {
  $vec = vec($traversable);
  $lo = 0;
  $hi = C\count($vec) - 1;
  while ($lo < $hi) {
    $temp = $vec[$lo];
    $vec[$lo++] = $vec[$hi];
    $vec[$hi--] = $temp;
  }
  return $vec;
}

/**
 * Returns a new vec with the values of the given Traversable in a random
 * order.
 */
function shuffle<Tv>(
  Traversable<Tv> $traversable,
): vec<Tv> {
  $vec = vec($traversable);
  \shuffle(&$vec);
  return $vec;
}

/**
 * Returns a new vec sorted by the values of the given Traversable. If the
 * optional comparator function isn't provided, the values will be sorted in
 * ascending order.
 *
 * To sort by some computable property of each value, see `Vec\sort_by()`.
 */
function sort<Tv>(
  Traversable<Tv> $traversable,
  ?(function(Tv, Tv): int) $comparator = null,
): vec<Tv> {
  $vec = vec($traversable);
  if ($comparator) {
    \usort(&$vec, $comparator);
  } else {
    \sort(&$vec);
  }
  return $vec;
}

/**
 * Returns a new vec sorted by some scalar property of each value of the given
 * Traversable, which is computed by the given function. If the optional
 * comparator function isn't provided, the values will be sorted in ascending
 * order of scalar key.
 *
 * To sort by the values of the Traversable, see `Vec\sort()`.
 */
function sort_by<Tv, Ts>(
  Traversable<Tv> $traversable,
  (function(Tv): Ts) $scalar_func,
  ?(function(Ts, Ts): int) $comparator = null,
): vec<Tv> {
  $vec = vec($traversable);
  $order_by = Dict\map($vec, $scalar_func);
  if ($comparator) {
    \uasort(&$order_by, $comparator);
  } else {
    \asort(&$order_by);
  }
  return namespace\map_with_key($order_by, ($k, $v) ==> $vec[$k]);
}
