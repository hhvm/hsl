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

/**
 * Returns a vec containing the original dict split into chunks of the given
 * size. If the original dict doesn't divide evenly, the final chunk will be
 * smaller.
 */
function chunk<Tk as arraykey, Tv>(
  KeyedTraversable<Tk, Tv> $traversable,
  int $size,
): vec<dict<Tk, Tv>> {
  invariant($size > 0, 'Expected positive chunk size, got %d.', $size);
  $result = vec[];
  $ii = 0;
  foreach ($traversable as $key => $value) {
    if ($ii % $size === 0) {
      $result[] = dict[];
    }
    $result[\intdiv($ii, $size)][$key] = $value;
    $ii++;
  }
  return $result;
}

/**
 * Returns a new dict mapping each value to the number of times it appears
 * in the given Traversable.
 */
function count_values<Tv as arraykey>(
  Traversable<Tv> $values,
): dict<Tv, int> {
  $result = dict[];
  foreach ($values as $value) {
    $result[$value] = idx($result, $value, 0) + 1;
  }
  return $result;
}

/**
 * Returns a new dict formed by merging the KeyedTraversable elements of the
 * given Traversable. In the case of duplicate keys, later values will overwrite
 * the previous ones.
 *
 * For a fixed number of KeyedTraversables, see `Dict\merge`.
 */
function flatten<Tk as arraykey, Tv>(
  Traversable<KeyedTraversable<Tk, Tv>> $traversables,
): dict<Tk, Tv> {
  $result = dict[];
  foreach ($traversables as $traversable) {
    foreach ($traversable as $key => $value) {
      $result[$key] = $value;
    }
  }
  return $result;
}

/**
 * Returns a new dict where all the given keys map to the given value.
 */
function fill_keys<Tk as arraykey, Tv>(
  Traversable<Tk> $keys,
  Tv $value,
): dict<Tk, Tv> {
  $result = dict[];
  foreach ($keys as $key) {
    $result[$key] = $value;
  }
  return $result;
}

/**
 * Returns a new dict keyed by the values of the given KeyedTraversable
 * and vice-versa. In case of duplicate values, later keys overwrite the
 * previous ones.
 */
function flip<Tk, Tv as arraykey>(
  KeyedTraversable<Tk, Tv> $traversable,
): dict<Tv, Tk> {
  $result = dict[];
  foreach ($traversable as $key => $value) {
    $result[$value] = $key;
  }
  return $result;
}

/**
 * Returns a new dict where each value is the result of calling the given
 * function on the corresponding key.
 *
 * To use an async function, see Dict\from_keys_async.
 * To create a dict from values, see Dict\from_values.
 * To create a dict from key/value pairs, see Dict\from_entries.
 */
function from_keys<Tk as arraykey, Tv>(
  Traversable<Tk> $keys,
  (function(Tk): Tv) $value_func,
): dict<Tk, Tv> {
  $result = dict[];
  foreach ($keys as $key) {
    $result[$key] = $value_func($key);
  }
  return $result;
}

/**
 * Returns a new dict where each mapping is defined by the given key/value
 * tuples. In the case of duplicate keys, later values will overwrite the
 * previous ones.
 *
 * To create a dict from keys, see Dict\from_keys.
 * To create a dict from values, see Dict\from_values.
 */
function from_entries<Tk as arraykey, Tv>(
  Traversable<(Tk, Tv)> $entries,
): dict<Tk, Tv> {
  $result = dict[];
  foreach ($entries as list($key, $value)) {
    $result[$key] = $value;
  }
  return $result;
}

/**
 * Returns a new dict keyed by the result of calling the given function on each
 * corresponding value. In the case of duplicate keys, later values will
 * overwrite the previous ones.
 *
 * To create a dict from keys, see Dict\from_keys.
 * To create a dict from key/value pairs, see Dict\from_entries.
 */
function from_values<Tk as arraykey, Tv>(
  Traversable<Tv> $values,
  (function(Tv): Tk) $key_func,
): dict<Tk, Tv> {
  $result = dict[];
  foreach ($values as $value) {
    $result[$key_func($value)] = $value;
  }
  return $result;
}

 /**
  * Returns a new dict where
  *  - keys are the results of the given function called on the given values.
  *  - values are vecs of original values that all produced the same key.
  * If a value produces a null key, it's omitted from the result.
  */
function group_by<Tk as arraykey, Tv>(
  Traversable<Tv> $values,
  (function(Tv): ?Tk) $key_func,
): dict<Tk, vec<Tv>> {
  $result = dict[];
  foreach ($values as $value) {
    $key = $key_func($value);
    if ($key === null) {
      continue;
    }
    if (!\array_key_exists($key, $result)) {
      $result[$key] = vec[];
    }
    $result[$key][] = $value;
  }
  return $result;
}

/**
 * Returns a new dict where each value is the result of calling the given
 * function on the original value.
 *
 * To use an async function, see Dict\map_async.
 */
function map<Tk as arraykey, Tv1, Tv2>(
  KeyedTraversable<Tk, Tv1> $traversable,
  (function(Tv1): Tv2) $value_func,
): dict<Tk, Tv2> {
  $result = dict[];
  foreach ($traversable as $key => $value) {
    $result[$key] = $value_func($value);
  }
  return $result;
}

/**
 * Returns a new dict where each key is the result of calling the given
 * function on the original key. In the case of duplicate keys, later values
 * will overwrite the previous ones.
 */
function map_keys<Tk1, Tk2 as arraykey, Tv>(
  KeyedTraversable<Tk1, Tv> $traversable,
  (function(Tk1): Tk2) $key_func,
): dict<Tk2, Tv> {
  $result = dict[];
  foreach ($traversable as $key => $value) {
    $result[$key_func($key)] = $value;
  }
  return $result;
}

/**
 * Returns a new dict where each value is the result of calling the given
 * function on the original value and key.
 */
function map_with_key<Tk as arraykey, Tv1, Tv2>(
  KeyedTraversable<Tk, Tv1> $traversable,
  (function(Tk, Tv1): Tv2) $value_func,
): dict<Tk, Tv2> {
  $result = dict[];
  foreach ($traversable as $key => $value) {
    $result[$key] = $value_func($key, $value);
  }
  return $result;
}

/**
 * Returns a new dict where:
 *  - values are the result of calling `$value_func` on the original value
 *  - keys are the result of calling `$key_func` on the original value.
 * In the case of duplicate keys, later values will overwrite the previous ones.
 */
function pull<Tk as arraykey, Tv1, Tv2>(
  Traversable<Tv1> $traversable,
  (function(Tv1): Tv2) $value_func,
  (function(Tv1): Tk) $key_func,
): dict<Tk, Tv2> {
  $result = dict[];
  foreach ($traversable as $value) {
    $result[$key_func($value)] = $value_func($value);
  }
  return $result;
}

/**
 * Returns a new dict where:
 *  - values are the result of calling `$value_func` on the original value/key
 *  - keys are the result of calling `$key_func` on the original value/key.
 * In the case of duplicate keys, later values will overwrite the previous ones.
 */
function pull_with_key<Tk1, Tk2 as arraykey, Tv1, Tv2>(
  KeyedTraversable<Tk1, Tv1> $traversable,
  (function(Tk1, Tv1): Tv2) $value_func,
  (function(Tk1, Tv1): Tk2) $key_func,
): dict<Tk2, Tv2> {
  $result = dict[];
  foreach ($traversable as $key => $value) {
    $result[$key_func($key, $value)] = $value_func($key, $value);
  }
  return $result;
}
