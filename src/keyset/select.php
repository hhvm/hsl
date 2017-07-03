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

/* HH_IGNORE_ERROR[5547] Hack Standard Lib is an exception */
namespace HH\Lib\Keyset;

/**
 * Returns a new keyset containing only the elements of the first Traversable
 * that do not appear in any of the other ones.
 */
function diff<Tv1 as arraykey, Tv2 as arraykey>(
  Traversable<Tv1> $first,
  Traversable<Tv2> $second,
  Traversable<Tv2> ...$rest
): keyset<Tv1> {
  if (!$first) {
    return keyset[];
  }
  if (!$second && !$rest) {
    return keyset($first);
  }
  $union = !$rest
    ? keyset($second)
    : namespace\union($second, ...$rest);
  return namespace\filter(
    $first,
    $value ==> !\array_key_exists($value, $union),
  );
}

/**
 * Returns a new keyset containing only the values for which the given predicate
 * returns `true`. The default predicate is casting the value to boolean.
 *
 * To remove null values in a typechecker-visible way, see Keyset\filter_nulls.
 */
function filter<Tv as arraykey>(
  Traversable<Tv> $traversable,
  ?(function(Tv): bool) $value_predicate = null,
): keyset<Tv> {
  $value_predicate = $value_predicate ?? fun('boolval');
  $result = keyset[];
  foreach ($traversable as $value) {
    if ($value_predicate($value)) {
      $result[] = $value;
    }
  }
  return $result;
}

/**
 * Returns a new keyset containing only non-null values of the given
 * Traversable.
 */
function filter_nulls<Tv as arraykey>(
  Traversable<?Tv> $traversable,
): keyset<Tv> {
  $result = keyset[];
  foreach ($traversable as $value) {
    if ($value !== null) {
      $result[] = $value;
    }
  }
  return $result;
}

/**
 * Returns a new keyset containing the keys of the given KeyedTraversable,
 * maintaining the iteration order.
 */
function keys<Tk as arraykey, Tv>(
  KeyedTraversable<Tk, Tv> $traversable,
): keyset<Tk> {
  $result = keyset[];
  foreach ($traversable as $key => $_) {
    $result[] = $key;
  }
  return $result;
}

/**
 * Returns a new keyset containing only the keys of the given KeyedTraversable
 * that map to truthy values.
 */
function keys_with_truthy_values<Tk as arraykey, Tv>(
  KeyedTraversable<Tk, Tv> $traversable,
): keyset<Tk> {
  $result = keyset[];
  foreach ($traversable as $key => $value) {
    if ($value) {
      $result[] = $key;
    }
  }
  return $result;
}

/**
 * Returns a new keyset containing only the elements of the first Traversable
 * that appear in all the other ones.
 */
function intersect<Tv as arraykey>(
  Traversable<Tv> $first,
  Traversable<Tv> $second,
  Traversable<Tv> ...$rest
): keyset<Tv> {
  if (!$second && !$rest) {
    return keyset[];
  }
  $intersection = keyset($first);
  $rest[] = $second;
  foreach ($rest as $traversable) {
    $next_intersection = keyset[];
    foreach ($traversable as $value) {
      if (\array_key_exists($value, $intersection)) {
        $next_intersection[] = $value;
      }
    }
    $intersection = $next_intersection;
  }
  return $intersection;
}

/**
 * Returns a new keyset containing the subsequence of the given Traversable
 * determined by the offset and length.
 *
 * If no length is given or it exceeds the upper bound of the Traversable,
 * the keyset will contain every element after the offset.
 *
 * If there are duplicate values in the Traversable, the keyset may be shorter
 * than the specified length.
 */
function slice<Tv as arraykey>(
  Container<Tv> $container,
  int $offset,
  ?int $length = null,
): keyset<Tv> {
  invariant($offset >= 0, 'Expected non-negative offset.');
  invariant($length === null || $length >= 0, 'Expected non-negative length.');
  return keyset(\array_slice($container, $offset, $length));
}
