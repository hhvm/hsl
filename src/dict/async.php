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

async function from_async<Tk as arraykey, Tv>(
  KeyedTraversable<Tk, Awaitable<Tv>> $awaitables,
): Awaitable<dict<Tk, Tv>> {
  $awaitables_ = dict($awaitables);

  /* HH_IGNORE_ERROR[4110] Okay to pass in Awaitable */
  /* HH_FIXME[4200] Hide the magic from reactivity */
  await AwaitAllWaitHandle::fromDict($awaitables_);
  foreach ($awaitables_ as $key => $value) {
    /* HH_IGNORE_ERROR[4110] Reuse the existing dict to reduce peak memory. */
    /* HH_FIXME[4248] unawaited Awaitable type value in reactive code */
    /* HH_FIXME[4200] Hide the magic from reactivity */
    $awaitables_[$key] = \HH\Asio\result($value);
  }
  /* HH_IGNORE_ERROR[4110] Reuse the existing dict to reduce peak memory. */
  return $awaitables_;
}

/**
 * Returns a new dict where each value is the result of calling the given
 * async function on the corresponding key.
 *
 * For non-async functions, see `Dict\from_keys()`.
 */
<<__Rx, __AtMostRxAsArgs>>
async function from_keys_async<Tk as arraykey, Tv>(
  <<__MaybeMutable, __OnlyRxIfImpl(\HH\Rx\Traversable::class)>>
  Traversable<Tk> $keys,
  <<__AtMostRxAsFunc>>
  (function(Tk): Awaitable<Tv>) $async_func,
): Awaitable<dict<Tk, Tv>> {
  $awaitables = dict[];
  foreach ($keys as $key) {
    /* HH_FIXME[4015] one of the few places it's OK to accumulate Awaitables */
    /* HH_FIXME[4248] non-awaited awaitable in rx context */
    $awaitables[$key] ??= $async_func($key);
  }
  /* HH_IGNORE_ERROR[4135] Unset local variable to reduce peak memory. */
  unset($keys);

  /* HH_IGNORE_ERROR[4110] Okay to pass in Awaitable */
    /* HH_FIXME[4200] Hide the magic from reactivity */
  await AwaitAllWaitHandle::fromDict($awaitables);
  foreach ($awaitables as $key => $value) {
    /* HH_IGNORE_ERROR[4110] Reuse the existing dict to reduce peak memory. */
    /* HH_FIXME[4248] unawaited Awaitable type value in reactive code */
    /* HH_FIXME[4200] Hide the magic from reactivity */
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
<<__Rx, __AtMostRxAsArgs>>
async function filter_async<Tk as arraykey, Tv>(
  KeyedContainer<Tk, Tv> $traversable,
  <<__AtMostRxAsFunc>>
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
<<__Rx, __AtMostRxAsArgs>>
async function filter_with_key_async<Tk as arraykey, Tv>(
  KeyedContainer<Tk, Tv> $traversable,
  <<__AtMostRxAsFunc>>
  (function(Tk, Tv): Awaitable<bool>) $predicate,
): Awaitable<dict<Tk, Tv>> {
  $tests = await map_with_key_async(
    $traversable,
    async ($k, $v) ==> await $predicate($k, $v),
  );
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
<<__Rx, __AtMostRxAsArgs>>
async function map_async<Tk as arraykey, Tv1, Tv2>(
  <<__MaybeMutable, __OnlyRxIfImpl(\HH\Rx\KeyedTraversable::class)>>
  KeyedTraversable<Tk, Tv1> $traversable,
  <<__AtMostRxAsFunc>>
  (function(Tv1): Awaitable<Tv2>) $value_func,
): Awaitable<dict<Tk, Tv2>> {
  $dict = dict($traversable);
  foreach ($dict as $key => $value) {
    /* HH_FIXME[4248] AwaitAllWaitHandle::fromDict is like await */
    /* HH_FIXME[4110] Reusing traversable for AwaitAllWaitHandle */
    $dict[$key] = $value_func($value);
  }

  /* HH_IGNORE_ERROR[4110] Okay to pass in Awaitable */
  /* HH_FIXME[4200] Hide the magic from reactivity */
  await AwaitAllWaitHandle::fromDict($dict);
  foreach ($dict as $key => $value) {
    /* HH_IGNORE_ERROR[4110] Reuse the existing dict to reduce peak memory. */
    /* HH_FIXME[4248] unawaited Awaitable type value in reactive code */
    /* HH_FIXME[4200] Hide the magic from reactivity */
    $dict[$key] = \HH\Asio\result($value);
  }
  /* HH_IGNORE_ERROR[4110] Reuse the existing dict to reduce peak memory. */
  return $dict;
}

<<__Rx, __AtMostRxAsArgs>>
async function map_with_key_async<Tk as arraykey, Tv1, Tv2>(
  <<__MaybeMutable, __OnlyRxIfImpl(\HH\Rx\KeyedTraversable::class)>>
  KeyedTraversable<Tk, Tv1> $container,
  <<__AtMostRxAsFunc>>
  (function(Tk, Tv1): Awaitable<Tv2>) $async_func
): Awaitable<dict<Tk, Tv2>> {
  $awaitables = Dict\map_with_key(
    $container,
    $async_func,
  );
  /* HH_IGNORE_ERROR[4135] Unset local variable to reduce peak memory. */
  unset($container);
  /* HH_IGNORE_ERROR[4110] Okay to pass in Awaitable */
  /* HH_FIXME[4200] Hide the magic from reactivity */
  await AwaitAllWaitHandle::fromDict($awaitables);
  foreach ($awaitables as $index => $value) {
    /* HH_IGNORE_ERROR[4110] Reuse the existing dict to reduce peak memory. */
    /* HH_FIXME[4248] unawaited Awaitable type value in reactive code */
    /* HH_FIXME[4200] Hide the magic from reactivity */
    $awaitables[$index] = \HH\Asio\result($value);
  }
  /* HH_IGNORE_ERROR[4110] Reuse the existing dict to reduce peak memory. */
  return $awaitables;
}
