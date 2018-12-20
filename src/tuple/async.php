<?hh // strict
/*
 *  Copyright (c) 2004-present, Facebook, Inc.
 *  All rights reserved.
 *
 *  This source code is licensed under the MIT license found in the
 *  LICENSE file in the root directory of this source tree.
 *
 */

namespace HH\Lib\Tuple;

/**
 * Create an awaitable tuple from variadic awaitables.
 *
 * Given `(Awaitable<T1>, Awaitable<T2>, ...)`, returns
 * `Awaitable(T1, T2, ...)`.
 *
 * Nullable Awaitables are also supported:
 * `(?Awaitable<T1>, ?Awaitable<T2>, ...)` is transformed to
 * `Awaitable<(?T1, ?T2)>`
 *
 * This is particularly useful when combined with list assignment:
 *
 * ```Hack
 * list($a, $b, $c) = await Tuple\from_async(
 *   foo_async(),
 *   bar_async(),
 *   baz_async(),
 * );
 * ```
 *
 * The function signature here is inaccurate as it can not be correctly
 * expressed in Hack; this function is special-cased in the typechecker.
 */
async function from_async(?Awaitable<mixed> ...$awaitables): Awaitable<mixed> {
  /* The oss-enable/disable + vec/varray dance is because varray is banned
   * externally, and HH_IGNORE_ERROR/HH_FIXME/UNSAFE_EXPR can't be used to
   * bypass the ban. */

  // @oss-disable: $awaitables = \varray($awaitables);
  $awaitables = vec($awaitables); // @oss-enable

  foreach ($awaitables as $index => $value) {
    if ($value === null) {
      $awaitables[$index] = async { return null; };
    }
  }

  /* HH_IGNORE_ERROR[4110] Hack can't infer that $awaitables has no nulls */
  // @oss-disable: await AwaitAllWaitHandle::fromVArray($awaitables);
  await AwaitAllWaitHandle::fromVec($awaitables); // @oss-enable
  foreach ($awaitables as $index => $value) {
    /* HH_IGNORE_ERROR[4110] Reuse the existing array to reduce peak memory. */
    $awaitables[$index] = \HH\Asio\result($value);
  }
  return \HH\Lib\_Private\tuple_from_vec($awaitables); // @oss-enable
  // @oss-disable: return $awaitables;
}
