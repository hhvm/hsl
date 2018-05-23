<?hh // strict
/*
 *  Copyright (c) 2004-present, Facebook, Inc.
 *  All rights reserved.
 *
 *  This source code is licensed under the MIT license found in the
 *  LICENSE file in the root directory of this source tree.
 *
 */

namespace HH\Lib\Vec;

<<__RxLocal>>
async function from_async<Tv>(
  Traversable<Awaitable<Tv>> $awaitables,
): Awaitable<vec<Tv>> {
  $awaitables = vec($awaitables);

  /* HH_IGNORE_ERROR[4110] Okay to pass in Awaitable */
  await AwaitAllWaitHandle::fromVec($awaitables);
  foreach ($awaitables as $index => $value) {
    /* HH_IGNORE_ERROR[4110] Reuse the existing vec to reduce peak memory. */
    $awaitables[$index] = \HH\Asio\result($value);
  }
  /* HH_IGNORE_ERROR[4110] Reuse the existing vec to reduce peak memory. */
  return $awaitables;
}

/**
 * Returns a new vec containing only the values for which the given async
 * predicate returns `true`.
 *
 * For non-async predicates, see `Vec\filter()`.
 */
<<__RxLocal>>
async function filter_async<Tv>(
  Container<Tv> $container,
  (function(Tv): Awaitable<bool>) $value_predicate,
): Awaitable<vec<Tv>> {
  $tests = await map_async($container, $value_predicate);
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
 * For non-async functions, see `Vec\map()`.
 */
<<__RxLocal>>
async function map_async<Tv1, Tv2>(
  Traversable<Tv1> $traversable,
  (function(Tv1): Awaitable<Tv2>) $async_func,
): Awaitable<vec<Tv2>> {
  $awaitables = vec[];
  foreach ($traversable as $value) {
    /* HH_FIXME[4248] AwaitAllWaitHandle::fromVec is like await */
    $awaitables[] = $async_func($value);
  }
  /* HH_IGNORE_ERROR[4135] Unset local variable to reduce peak memory. */
  unset($traversable);

  /* HH_IGNORE_ERROR[4110] Okay to pass in Awaitable */
  await AwaitAllWaitHandle::fromVec($awaitables);
  foreach ($awaitables as $index => $value) {
    /* HH_IGNORE_ERROR[4110] Reuse the existing vec to reduce peak memory. */
    $awaitables[$index] = \HH\Asio\result($value);
  }
  /* HH_IGNORE_ERROR[4110] Reuse the existing vec to reduce peak memory. */
  return $awaitables;
}
