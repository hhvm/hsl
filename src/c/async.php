<?hh // strict
/*
 *  Copyright (c) 2004-present, Facebook, Inc.
 *  All rights reserved.
 *
 *  This source code is licensed under the MIT license found in the
 *  LICENSE file in the root directory of this source tree.
 *
 */

/** 
 * C is for Containers. This file contains async functions that operate on containers
 * and traversables.
 */
namespace HH\Lib\C;

/**
 * Returns the first element of the result of the given Awaitable, or null if
 * the Traversable is empty.
 *
 * For non-Awaitable Traversables, see `C\first`.
 */
async function first_async<T>(
  Awaitable<Traversable<T>> $awaitable,
): Awaitable<?T> {
  $traversable = await $awaitable;
  return namespace\first($traversable);
}

/**
 * Returns the first element of the result of the given Awaitable, or throws if
 * the Traversable is empty.
 *
 * For non-Awaitable Traversables, see `C\firstx`.
 */
async function firstx_async<T>(
  Awaitable<Traversable<T>> $awaitable,
): Awaitable<T> {
  $traversable = await $awaitable;
  return namespace\firstx($traversable);
}
