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
 * C is for Containers. This file contains functions that ask
 * questions of (i.e. introspect) containers and traversables.
 */
namespace HH\Lib\C;

/**
 * Returns true if the given predicate returns true for any element of the
 * given Traversable. If no predicate is provided, it defaults to casting the
 * element to bool.
 *
 * If you're looking for `C\none`, use `!C\any`.
 *
 * Time complexity: O(n)
 * Space complexity: O(1)
 */
<<__Rx, __AtMostRxAsArgs>>
function any<T>(
  <<__MaybeMutable, __OnlyRxIfImpl(\HH\Rx\Traversable::class)>>
  Traversable<T> $traversable,
  <<__AtMostRxAsFunc>>
  ?(function(T): bool) $predicate = null,
): bool {
  $predicate ??= fun('\\HH\\Lib\\_Private\\boolval');
  foreach ($traversable as $value) {
    if ($predicate($value)) {
      return true;
    }
  }
  return false;
}

/**
 * Returns true if the given Traversable contains the value. Strict equality is
 * used.
 *
 * Time complexity: O(n)
 * Space complexity: O(1)
 */
<<__Rx, __AtMostRxAsArgs>>
function contains<T>(
  <<__MaybeMutable, __OnlyRxIfImpl(\HH\Rx\Traversable::class)>>
  Traversable<T> $traversable,
  T $value,
): bool {
  if ($traversable is keyset<_>) {
    /* HH_IGNORE_ERROR[4110] It's nonsensical for `$value` to not be an arraykey
     * in this case because a `keyset<_>` can never contain anything other than
     * `arraykey`s. However, Hack allows you to write something like:
     *
     *   C\contains(keyset[], new Foobar())
     *
     * HHVM will throw if you try to call `array_key_exists` (`C\contains_key`
     * calls this) on any Hack array and an invalid array key _except_ null.
     * Because `C\contains` on a keyset calls `C\contains_key`:
     *
     *   C\contains(keyset[], new Foobar()); // Throws
     *   C\contains(keyset[], 4.2);          // Throws
     *   C\contains(keyset[], null);         // Does not throw: is always false
     *
     * This is subtle behavior that we'd rather just let HHVM handle for now.
     */
    return contains_key($traversable, $value);
  }
  foreach ($traversable as $v) {
    if ($value === $v) {
      return true;
    }
  }
  return false;
}

/**
 * Returns true if the given KeyedContainer contains the key.
 *
 * Time complexity: O(1)
 * Space complexity: O(1)
 */
<<__Rx>>
function contains_key<Tk as arraykey, Tv>(
  <<__MaybeMutable>> KeyedContainer<Tk, Tv> $container,
  Tk $key,
): bool {
  /* HH_IGNORE_ERROR[2049] __PHPStdLib */
  /* HH_IGNORE_ERROR[4107] __PHPStdLib */
  return \array_key_exists($key, $container);
}

/**
 * Returns the number of elements in the given Container.
 *
 * Time complexity: O(1)
 * Space complexity: O(1)
 */
<<__Rx>>
function count<T>(
  <<__MaybeMutable>> Container<T> $container,
): int {
  /* HH_IGNORE_ERROR[2049] __PHPStdLib */
  /* HH_IGNORE_ERROR[4107] __PHPStdLib */
  return \count($container);
}

/**
 * Returns true if the given predicate returns true for every element of the
 * given Traversable. If no predicate is provided, it defaults to casting the
 * element to bool.
 *
 * If you're looking for `C\all`, this is it.
 *
 * Time complexity: O(n)
 * Space complexity: O(1)
 */
<<__Rx, __AtMostRxAsArgs>>
function every<T>(
  <<__MaybeMutable, __OnlyRxIfImpl(\HH\Rx\Traversable::class)>>
  Traversable<T> $traversable,
  <<__AtMostRxAsFunc>>
  ?(function(T): bool) $predicate = null,
): bool {
  $predicate ??= fun('\\HH\\Lib\\_Private\\boolval');
  foreach ($traversable as $value) {
    if (!$predicate($value)) {
      return false;
    }
  }
  return true;
}

/**
 * Returns whether the given Container is empty.
 *
 * Time complexity: O(1)
 * Space complexity: O(1)
 */
<<__Rx>>
function is_empty<T>(
  Container<T> $container,
): bool {
  if ($container is \ConstCollection<_>) {
    return $container->isEmpty();
  }
  return !$container;
}
