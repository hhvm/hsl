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
async function from_async(?Awaitable<mixed> ...$args): Awaitable<mixed> {
  /* The oss-enable/disable + vec/varray dance is because varray is banned
   * externally, and HH_IGNORE_ERROR/HH_FIXME/UNSAFE_EXPR can't be used to
   * bypass the ban. */

  // @oss-disable: $wait_handles = varray[];
  $wait_handles = vec[]; // @oss-enable

  foreach ($args as $value) {
    if ($value === null) {
      // Calling ->getWaitHandle so that Hack knows this is WaitHandle (it's
      // safe because HHVM will optimize it away).
      $async_null = async { return null; };
      $wait_handles[] = $async_null->getWaitHandle();
    } else {
      $wait_handles[] = $value instanceof WaitHandle
        ? $value
        : $value->getWaitHandle();
    }
  }
  /* HH_IGNORE_ERROR[4135] unset() is not supposed to be used here, but we want
   * the memory back */
  unset($args);
  // @oss-disable: await AwaitAllWaitHandle::fromArray($wait_handles);
  await AwaitAllWaitHandle::fromVec($wait_handles); // @oss-enable
  foreach ($wait_handles as $index => $value) {
    /* HH_IGNORE_ERROR[4110] Reuse the existing array to reduce peak memory. */
    $wait_handles[$index] = \HH\Asio\result($value);
  }
  return \HH\Lib\_Private\tuple_from_vec($wait_handles); // @oss-enable
  // @oss-disable: return $wait_handles;
}
