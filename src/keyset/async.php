<?hh // strict
/*
 *  Copyright (c) 2004-present, Facebook, Inc.
 *  All rights reserved.
 *
 *  This source code is licensed under the MIT license found in the
 *  LICENSE file in the root directory of this source tree.
 *
 */

namespace HH\Lib\Keyset;

use namespace HH\Lib\Vec;

/**
 * Returns a new keyset containing the awaited result of the given Awaitables.
 *
 * Time complexity: O(n * a), where a is the complexity of each Awaitable
 * Space complexity: O(n)
 */
async function from_async<Tv as arraykey>(
  Traversable<Awaitable<Tv>> $awaitables,
): Awaitable<keyset<Tv>> {
  $vec = await Vec\from_async($awaitables);
  return keyset($vec);
}

/**
 * Returns a new keyset containing only the values for which the given async
 * predicate returns `true`.
 *
 * For non-async predicates, see `Keyset\filter()`.
 *
 * Time complexity: O(n * p), where p is the complexity of `$value_predicate`
 * Space complexity: O(n)
 */
<<__Rx, __AtMostRxAsArgs>>
async function filter_async<Tv as arraykey>(
  Container<Tv> $traversable,
  <<__AtMostRxAsFunc>>
  (function(Tv): Awaitable<bool>) $value_predicate,
): Awaitable<keyset<Tv>> {
  $tests = await Vec\map_async($traversable, $value_predicate);
  $result = keyset[];
  $ii = 0;
  foreach ($traversable as $value) {
    if ($tests[$ii]) {
      $result[] = $value;
    }
    $ii++;
  }
  return $result;
}

/**
 * Returns a new keyset where the value is the result of calling the
 * given async function on the original values in the given traversable.
 *
 * Time complexity: O(n * f), where f is the complexity of `$async_func`
 * Space complexity: O(n)
 */
<<__Rx, __AtMostRxAsArgs>>
async function map_async<Tv, Tk as arraykey>(
  <<__MaybeMutable, __OnlyRxIfImpl(\HH\Rx\Traversable::class)>>
  Traversable<Tv> $traversable,
  <<__AtMostRxAsFunc>>
  (function(Tv): Awaitable<Tk>) $async_func,
): Awaitable<keyset<Tk>> {
  $vec = await Vec\map_async($traversable, $async_func);
  return keyset($vec);
}
