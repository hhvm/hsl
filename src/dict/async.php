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

use namespace HH\Lib\C;

async function from_async<Tk, Tv>(
  KeyedTraversable<Tk, Awaitable<Tv>> $awaitables,
): Awaitable<dict<Tk, Tv>> {
  $wait_handles = dict[];
  foreach ($awaitables as $key => $value) {
    $wait_handles[$key] = $value instanceof WaitHandle
      ? $value
      : $value->getWaitHandle();
  }
  /* HH_IGNORE_ERROR[4135] Unset local variable to reduce peak memory. */
  unset($awaitables);
  await AwaitAllWaitHandle::fromDict($wait_handles);
  foreach ($wait_handles as $key => $value) {
    /* HH_IGNORE_ERROR[4110] Reuse the existing dict to reduce peak memory. */
    $wait_handles[$key] = \HH\Asio\result($value);
  }
  /* HH_IGNORE_ERROR[4110] Reuse the existing dict to reduce peak memory. */
  return $wait_handles;
}

/**
 * Returns a new dict where each value is the result of calling the given
 * async function on the corresponding key.
 *
 * For non-async functions, see Dict\from_keys.
 */
async function from_keys_async<Tk as arraykey, Tv>(
  Traversable<Tk> $keys,
  (function(Tk): Awaitable<Tv>) $async_func,
): Awaitable<dict<Tk, Tv>> {
  $wait_handles = dict[];
  foreach ($keys as $key) {
    if (!C\contains_key($wait_handles, $key)) {
      $value = $async_func($key);
      $wait_handles[$key] = $value instanceof WaitHandle
        ? $value
        : $value->getWaitHandle();
    }
  }
  /* HH_IGNORE_ERROR[4135] Unset local variable to reduce peak memory. */
  unset($keys);
  await AwaitAllWaitHandle::fromDict($wait_handles);
  foreach ($wait_handles as $key => $value) {
    /* HH_IGNORE_ERROR[4110] Reuse the existing dict to reduce peak memory. */
    $wait_handles[$key] = \HH\Asio\result($value);
  }
  /* HH_IGNORE_ERROR[4110] Reuse the existing dict to reduce peak memory. */
  return $wait_handles;
}

/**
 * Returns a new dict containing only the values for which the given async
 * predicate returns `true`.
 *
 * For non-async predicates, see Dict\filter.
 */
async function filter_async<Tk as arraykey, Tv>(
  KeyedContainer<Tk, Tv> $traversable,
  (function(Tv): Awaitable<bool>) $value_predicate,
): Awaitable<dict<Tk, Tv>> {
  $tests = await namespace\map_async($traversable, $value_predicate);
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
 * For non-async filters with key, see Dict\filter_with_key.
 */
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
 * For non-async functions, see Dict\map.
 */
async function map_async<Tk, Tv1, Tv2>(
  KeyedTraversable<Tk, Tv1> $traversable,
  (function(Tv1): Awaitable<Tv2>) $value_func,
): Awaitable<dict<Tk, Tv2>> {
  $wait_handles = dict[];
  foreach ($traversable as $key => $value) {
    $value = $value_func($value);
    $wait_handles[$key] = $value instanceof WaitHandle
      ? $value
      : $value->getWaitHandle();
  }
  /* HH_IGNORE_ERROR[4135] Unset local variable to reduce peak memory. */
  unset($traversable);
  await AwaitAllWaitHandle::fromDict($wait_handles);
  foreach ($wait_handles as $key => $value) {
    /* HH_IGNORE_ERROR[4110] Reuse the existing dict to reduce peak memory. */
    $wait_handles[$key] = \HH\Asio\result($value);
  }
  /* HH_IGNORE_ERROR[4110] Reuse the existing dict to reduce peak memory. */
  return $wait_handles;
}
