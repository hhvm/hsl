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

use namespace HH\Lib\C;

<<__RxLocal>>
async function from_async<Tk as arraykey, Tv>(
  KeyedTraversable<Tk, Awaitable<Tv>> $awaitables,
): Awaitable<dict<Tk, Tv>> {
  $awaitables = dict($awaitables);

  /* HH_IGNORE_ERROR[4110] Okay to pass in Awaitable */
  await AwaitAllWaitHandle::fromDict($awaitables);
  foreach ($awaitables as $key => $value) {
    /* HH_IGNORE_ERROR[4110] Reuse the existing dict to reduce peak memory. */
    $awaitables[$key] = \HH\Asio\result($value);
  }
  /* HH_IGNORE_ERROR[4110] Reuse the existing dict to reduce peak memory. */
  return $awaitables;
}

/**
 * Returns a new dict where each value is the result of calling the given
 * async function on the corresponding key.
 *
 * For non-async functions, see `Dict\from_keys()`.
 */
<<__RxLocal>>
async function from_keys_async<Tk as arraykey, Tv>(
  Traversable<Tk> $keys,
  (function(Tk): Awaitable<Tv>) $async_func,
): Awaitable<dict<Tk, Tv>> {
  $awaitables = dict[];
  foreach ($keys as $key) {
    if (!C\contains_key($awaitables, $key)) {
      $awaitables[$key] = $async_func($key);
    }
  }
  /* HH_IGNORE_ERROR[4135] Unset local variable to reduce peak memory. */
  unset($keys);

  /* HH_IGNORE_ERROR[4110] Okay to pass in Awaitable */
  await AwaitAllWaitHandle::fromDict($awaitables);
  foreach ($awaitables as $key => $value) {
    /* HH_IGNORE_ERROR[4110] Reuse the existing dict to reduce peak memory. */
    $awaitables[$key] = \HH\Asio\result($value);
  }
  /* HH_IGNORE_ERROR[4110] Reuse the existing dict to reduce peak memory. */
  return $awaitables;
}

/**
 * Returns a new dict containing only the values for which the given async
 * predicate returns `true`.
 *
 * For non-async predicates, see `Dict\filter()`.
 */
<<__RxLocal>>
async function filter_async<Tk as arraykey, Tv>(
  KeyedContainer<Tk, Tv> $traversable,
  (function(Tv): Awaitable<bool>) $value_predicate,
): Awaitable<dict<Tk, Tv>> {
  $tests = await map_async($traversable, $value_predicate);
  $result = dict[];
  foreach ($traversable as $key => $value) {
    if ($tests[$key]) {
      $result[$key] = $value;
    }
  }
  return $result;
}

/**
 * Like filter_async, but lets you utilize the keys of your dict too.
 *
 * For non-async filters with key, see `Dict\filter_with_key()`.
 */
<<__RxLocal>>
async function filter_with_key_async<Tk as arraykey, Tv>(
  KeyedContainer<Tk, Tv> $traversable,
  (function(Tk, Tv): Awaitable<bool>) $predicate,
): Awaitable<dict<Tk, Tv>> {
  $tests = await $traversable
    |> map_with_key($$, ($k, $v) ==> $predicate($k, $v))
    |> from_async($$);
  $result = dict[];
  foreach ($tests as $k => $v) {
    if ($v) {
      $result[$k] = $traversable[$k];
    }
  }
  return $result;
}

/**
 * Returns a new dict where each value is the result of calling the given
 * async function on the original value.
 *
 * For non-async functions, see `Dict\map()`.
 */
<<__RxLocal>>
async function map_async<Tk as arraykey, Tv1, Tv2>(
  KeyedTraversable<Tk, Tv1> $traversable,
  (function(Tv1): Awaitable<Tv2>) $value_func,
): Awaitable<dict<Tk, Tv2>> {
  $awaitables = dict[];
  foreach ($traversable as $key => $value) {
    $awaitables[$key] = $value_func($value);
  }
  /* HH_IGNORE_ERROR[4135] Unset local variable to reduce peak memory. */
  unset($traversable);

  /* HH_IGNORE_ERROR[4110] Okay to pass in Awaitable */
  await AwaitAllWaitHandle::fromDict($awaitables);
  foreach ($awaitables as $key => $value) {
    /* HH_IGNORE_ERROR[4110] Reuse the existing dict to reduce peak memory. */
    $awaitables[$key] = \HH\Asio\result($value);
  }
  /* HH_IGNORE_ERROR[4110] Reuse the existing dict to reduce peak memory. */
  return $awaitables;
}
