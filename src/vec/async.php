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

async function from_async<Tv>(
  Traversable<Awaitable<Tv>> $awaitables,
): Awaitable<vec<Tv>> {
  $wait_handles = vec[];
  foreach ($awaitables as $value) {
    $wait_handles[] = $value instanceof WaitHandle
      ? $value
      : $value->getWaitHandle();
  }
  /* HH_IGNORE_ERROR[4135] Unset local variable to reduce peak memory. */
  unset($awaitables);
  await AwaitAllWaitHandle::fromVec($wait_handles);
  foreach ($wait_handles as $index => $value) {
    /* HH_IGNORE_ERROR[4110] Reuse the existing vec to reduce peak memory. */
    $wait_handles[$index] = \HH\Asio\result($value);
  }
  /* HH_IGNORE_ERROR[4110] Reuse the existing vec to reduce peak memory. */
  return $wait_handles;
}

/**
 * Returns a new vec containing only the values for which the given async
 * predicate returns `true`.
 *
 * For non-async predicates, see Vec\filter.
 */
async function filter_async<Tv>(
  Container<Tv> $container,
  (function(Tv): Awaitable<bool>) $value_predicate,
): Awaitable<vec<Tv>> {
  $tests = await namespace\map_async($container, $value_predicate);
  $result = vec[];
  $ii = 0;
  foreach ($container as $value) {
    if ($tests[$ii]) {
      $result[] = $value;
    }
    $ii++;
  }
  return $result;
}

/**
 * Returns a new vec where each value is the result of calling the given
 * async function on the original value.
 *
 * For non-async functions, see Vec\map.
 */
async function map_async<Tv1, Tv2>(
  Traversable<Tv1> $traversable,
  (function(Tv1): Awaitable<Tv2>) $async_func,
): Awaitable<vec<Tv2>> {
  $wait_handles = vec[];
  foreach ($traversable as $value) {
    $value = $async_func($value);
    $wait_handles[] = $value instanceof WaitHandle
      ? $value
      : $value->getWaitHandle();
  }
  /* HH_IGNORE_ERROR[4135] Unset local variable to reduce peak memory. */
  unset($traversable);
  await AwaitAllWaitHandle::fromVec($wait_handles);
  foreach ($wait_handles as $index => $value) {
    /* HH_IGNORE_ERROR[4110] Reuse the existing vec to reduce peak memory. */
    $wait_handles[$index] = \HH\Asio\result($value);
  }
  /* HH_IGNORE_ERROR[4110] Reuse the existing vec to reduce peak memory. */
  return $wait_handles;
}
