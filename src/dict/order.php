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

namespace HH\Lib\Dict;

use namespace HH\Lib\Vec;

/**
 * Returns a new dict with the original key/value pairs in reversed iteration
 * order.
 */
function reverse<Tk as arraykey, Tv>(
  KeyedTraversable<Tk, Tv> $traversable,
): dict<Tk, Tv> {
  $dict = dict($traversable);
  return $dict
    |> Vec\keys($$)
    |> Vec\reverse($$)
    |> namespace\from_keys($$, ($k) ==> $dict[$k]);
}

/**
 * Returns a new dict sorted by the values of the given KeyedTraversable. If the
 * optional comparator function isn't provided, the values will be sorted in
 * ascending order.
 *
 * To sort by some computable property of each value, see sort_by().
 */
function sort<Tk, Tv>(
  KeyedTraversable<Tk, Tv> $traversable,
  ?(function(Tv, Tv): int) $value_comparator = null,
): dict<Tk, Tv> {
  $result = dict($traversable);
  if ($value_comparator) {
    \uasort(/*ctpbr:&*/$result, $value_comparator);
  } else {
    \asort(/*ctpbr:&*/$result);
  }
  return $result;
}

/**
 * Returns a new dict sorted by some scalar property of each value of the given
 * KeyedTraversable, which is computed by the given function. If the optional
 * comparator function isn't provided, the values will be sorted in ascending
 * order of scalar key.
 */
function sort_by<Tk as arraykey, Tv, Ts>(
  KeyedTraversable<Tk, Tv> $traversable,
  (function(Tv): Ts) $scalar_func,
  ?(function(Ts, Ts): int) $scalar_comparator = null,
): dict<Tk, Tv> {
  $tuple_comparator = $scalar_comparator
    ? ($a, $b) ==> $scalar_comparator($a[0], $b[0])
    : ($a, $b) ==> $a[0] <=> $b[0];
  return $traversable
    |> namespace\map($$, $v ==> tuple($scalar_func($v), $v))
    |> namespace\sort($$, $tuple_comparator)
    |> namespace\map($$, $t ==> $t[1]);
}

/**
 * Returns a new dict sorted by the keys of the given KeyedTraversable. If the
 * optional comparator function isn't provided, the keys will be sorted in
 * ascending order.
 */
function sort_by_key<Tk, Tv>(
  KeyedTraversable<Tk, Tv> $traversable,
  ?(function(Tk, Tk): int) $key_comparator = null,
): dict<Tk, Tv> {
  $result = dict($traversable);
  if ($key_comparator) {
    \uksort(/*ctpbr:&*/$result, $key_comparator);
  } else {
    \ksort(/*ctpbr:&*/$result);
  }
  return $result;
}
