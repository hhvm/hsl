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
 */
<<__Rx, __OnlyRxIfArgs>>
function any<T>(
  <<__MaybeMutable, __OnlyRxIfImpl(\HH\Rx\Traversable::class)>>
  Traversable<T> $traversable,
  <<__OnlyRxIfRxFunc>>
  ?(function(T): bool) $predicate = null,
): bool {
  $predicate = $predicate ?? fun('\\HH\\Lib\\_Private\\boolval');
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
 */
<<__Rx, __OnlyRxIfArgs>>
function contains<T>(
  <<__MaybeMutable, __OnlyRxIfImpl(\HH\Rx\Traversable::class)>>
  Traversable<T> $traversable,
  T $value,
): bool {
  if (is_keyset($traversable)) {
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
 */
<<__Rx>>
function contains_key<Tk, Tv>(
  <<__MaybeMutable>> KeyedContainer<Tk, Tv> $container,
  Tk $key,
): bool {
  return \array_key_exists($key, $container);
}

/**
 * Returns the number of elements in the given Container.
 */
<<__Rx>>
function count<T>(
  <<__MaybeMutable>> Container<T> $container,
): int {
  return \count($container);
}

/**
 * Returns true if the given predicate returns true for every element of the
 * given Traversable. If no predicate is provided, it defaults to casting the
 * element to bool.
 */
<<__Rx, __OnlyRxIfArgs>>
function every<T>(
  <<__MaybeMutable, __OnlyRxIfImpl(\HH\Rx\Traversable::class)>>
  Traversable<T> $traversable,
  <<__OnlyRxIfRxFunc>>
  ?(function(T): bool) $predicate = null,
): bool {
  $predicate = $predicate ?? fun('\\HH\\Lib\\_Private\\boolval');
  foreach ($traversable as $value) {
    if (!$predicate($value)) {
      return false;
    }
  }
  return true;
}

/**
 * Returns whether the given Container is empty.
 */
<<__Rx>>
function is_empty<T>(
  Container<T> $container,
): bool {
  return !$container;
}
