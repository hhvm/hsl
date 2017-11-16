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

namespace HH\Lib\Keyset;

use namespace HH\Lib\Vec;

/**
 * Returns a new keyset containing the awaited result of the given Awaitables.
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
 */
async function filter_async<Tv as arraykey>(
  Container<Tv> $traversable,
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
 */
async function map_async<Tv, Tk as arraykey>(
  Traversable<Tv> $traversable,
  (function(Tv): Awaitable<Tk>) $async_func,
): Awaitable<keyset<Tk>> {
  $vec = await Vec\map_async($traversable, $async_func);
  return keyset($vec);
}
