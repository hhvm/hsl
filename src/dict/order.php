<?hh // strict
/*
 *  Copyright (c) 2004-present, Facebook, Inc.
 *  All rights reserved.
 *
 *  This source code is licensed under the MIT license found in the
 *  LICENSE file in the root directory of this source tree.
 *
 */

namespace HH\Lib\Dict;

use namespace HH\Lib\Vec;

/**
 * Returns a new dict with the original entries in reversed iteration
 * order.
 */
<<__Rx, __AtMostRxAsArgs>>
function reverse<Tk as arraykey, Tv>(
  <<__MaybeMutable, __OnlyRxIfImpl(\HH\Rx\KeyedTraversable::class)>>
  KeyedTraversable<Tk, Tv> $traversable,
): dict<Tk, Tv> {
  $dict = dict($traversable);
  return $dict
    |> Vec\keys($$)
    |> Vec\reverse($$)
    |> from_keys($$, <<__Rx>> ($k) ==> $dict[$k]);
}

/**
 * Returns a new dict sorted by the values of the given KeyedTraversable. If the
 * optional comparator function isn't provided, the values will be sorted in
 * ascending order.
 *
 * - To sort by some computable property of each value, see `Dict\sort_by()`.
 * - To sort by the keys of the KeyedTraversable, see `Dict\sort_by_key()`.
 */
<<__Rx, __AtMostRxAsArgs>>
function sort<Tk as arraykey, Tv>(
  <<__MaybeMutable, __OnlyRxIfImpl(\HH\Rx\KeyedTraversable::class)>>
  KeyedTraversable<Tk, Tv> $traversable,
  <<__AtMostRxAsFunc>>
  ?(function(Tv, Tv): int) $value_comparator = null,
): dict<Tk, Tv> {
  $result = dict($traversable);
  if ($value_comparator) {
    /* HH_FIXME[4200] is reactive */
    /* HH_FIXME[2088] No refs in reactive code. */
    /* HH_IGNORE_ERROR[2049] __PHPStdLib */
    /* HH_IGNORE_ERROR[4107] __PHPStdLib */
    \uasort(&$result, $value_comparator);
  } else {
    /* HH_FIXME[4200] is reactive */
    /* HH_FIXME[2088] No refs in reactive code. */
    /* HH_IGNORE_ERROR[2049] __PHPStdLib */
    /* HH_IGNORE_ERROR[4107] __PHPStdLib */
    \asort(&$result);
  }
  return $result;
}

/**
 * Returns a new dict sorted by some scalar property of each value of the given
 * KeyedTraversable, which is computed by the given function. If the optional
 * comparator function isn't provided, the values will be sorted in ascending
 * order of scalar key.
 *
 * - To sort by the values of the KeyedTraversable, see `Dict\sort()`.
 * - To sort by the keys of the KeyedTraversable, see `Dict\sort_by_key()`.
 */
<<__Rx, __AtMostRxAsArgs>>
function sort_by<Tk as arraykey, Tv, Ts>(
  <<__MaybeMutable, __OnlyRxIfImpl(\HH\Rx\KeyedTraversable::class)>>
  KeyedTraversable<Tk, Tv> $traversable,
  <<__AtMostRxAsFunc>>
  (function(Tv): Ts) $scalar_func,
  <<__AtMostRxAsFunc>>
  ?(function(Ts, Ts): int) $scalar_comparator = null,
): dict<Tk, Tv> {
  $tuple_comparator = $scalar_comparator
    ? <<__RxOfScope>> ((Ts, Tv) $a, (Ts, Tv) $b) ==>
      $scalar_comparator($a[0], $b[0])
    /* HH_FIXME[4240] need Scalar type */
    : (<<__Rx>>(Ts, Tv) $a, (Ts, Tv) $b) ==> $a[0] <=> $b[0];
  return $traversable
    |> map($$, <<__RxOfScope>> $v ==> tuple($scalar_func($v), $v))
    |> sort($$, $tuple_comparator)
    |> map($$, <<__Rx>> $t ==> $t[1]);
}

/**
 * Returns a new dict sorted by the keys of the given KeyedTraversable. If the
 * optional comparator function isn't provided, the keys will be sorted in
 * ascending order.
 *
 * - To sort by the values of the KeyedTraversable, see `Dict\sort()`.
 * - To sort by some computable property of each value, see `Dict\sort_by()`.
 */
<<__Rx, __AtMostRxAsArgs>>
function sort_by_key<Tk as arraykey, Tv>(
  <<__MaybeMutable, __OnlyRxIfImpl(\HH\Rx\KeyedTraversable::class)>>
  KeyedTraversable<Tk, Tv> $traversable,
  <<__AtMostRxAsFunc>>
  ?(function(Tk, Tk): int) $key_comparator = null,
): dict<Tk, Tv> {
  $result = dict($traversable);
  if ($key_comparator) {
    /* HH_FIXME[4200] is reactive */
    /* HH_FIXME[2088] No refs in reactive code. */
    /* HH_FIXME[2049] We are allowed to use PHP Stardard library functions */
    /* HH_FIXME[4107] We are allowed to use PHP Stardard library functions */
    \uksort(&$result, $key_comparator);
  } else {
    /* HH_FIXME[4200] is reactive */
    /* HH_FIXME[2088] No refs in reactive code. */
    \ksort(&$result);
  }
  return $result;
}
