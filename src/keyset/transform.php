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
 * Returns a new keyset where each value is the result of calling the given
 * function on the original value.
 */
function map<Tv1, Tv2 as arraykey>(
  Traversable<Tv1> $traversable,
  (function(Tv1): Tv2) $value_func,
): keyset<Tv2> {
  $result = keyset[];
  foreach ($traversable as $value) {
    $result[] = $value_func($value);
  }
  return $result;
}

/**
 * Returns a new keyset where each value is the result of calling the given
 * function on the original key and value.
 */
function map_with_key<Tk, Tv1, Tv2 as arraykey>(
  KeyedTraversable<Tk, Tv1> $traversable,
  (function(Tk, Tv1): Tv2) $value_func,
): keyset<Tv2> {
  $result = keyset[];
  foreach ($traversable as $key => $value) {
    $result[] = $value_func($key, $value);
  }
  return $result;
}

/**
 * Returns a new keyset formed by joining the values
 * within the given Traversables into
 * a keyset.
 *
 * For a fixed number of Traversables, see Keyset\union.
 */
function flatten<Tv as arraykey>(
  Traversable<Traversable<Tv>> $traversables,
): keyset<Tv> {
  $result = keyset[];
  foreach ($traversables as $traversable) {
    foreach ($traversable as $value) {
      $result[] = $value;
    }
  }
  return $result;
}
