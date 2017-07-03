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

/* @lint-ignore HackLint5547 Hack Standard Lib is an exception */
namespace HH\Lib\Dict;

use \HH\Lib\C;

/**
 * Returns a new dict where each element in `$keys` maps to the
 * corresponding element in `$values`.
 */
function associate<Tk as arraykey, Tv>(
  Traversable<Tk> $keys,
  Traversable<Tv> $values,
): dict<Tk, Tv> {
  $keys = vec($keys);
  $values = vec($values);
  invariant(
    C\count($keys) === C\count($values),
    'Expected length of keys and values to be the same',
  );
  $result = dict[];
  foreach ($keys as $idx => $key) {
    $result[$key] = $values[$idx];
  }
  return $result;
}

/**
 * Merges multiple KeyedTraversables into a new dict. In the case of duplicate
 * keys, later values will overwrite the previous ones.
 */
function merge<Tk, Tv>(
  KeyedTraversable<Tk, Tv> ...$traversables
): dict<Tk, Tv> {
  $result = dict[];
  foreach ($traversables as $traversable) {
    foreach ($traversable as $key => $value) {
      $result[$key] = $value;
    }
  }
  return $result;
}
