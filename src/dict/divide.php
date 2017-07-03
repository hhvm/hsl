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

/**
 * Returns a 2-tuple containing dicts for which the given predicate returned
 * `true` and `false`, respectively.
 */
function partition<Tk, Tv>(
  KeyedTraversable<Tk, Tv> $traversable,
  (function(Tv): bool) $predicate,
): (dict<Tk, Tv>, dict<Tk, Tv>) {
  $success = dict[];
  $failure = dict[];
  foreach ($traversable as $key => $value) {
    if ($predicate($value)) {
      $success[$key] = $value;
    } else {
      $failure[$key] = $value;
    }
  }
  return tuple($success, $failure);
}

/**
 * Returns a 2-tuple containing dicts for which the given keyed predicate
 * returned `true` and `false`, respectively.
 */
function partition_with_key<Tk, Tv>(
  KeyedTraversable<Tk, Tv> $traversable,
  (function(Tk, Tv): bool) $predicate,
): (dict<Tk, Tv>, dict<Tk, Tv>) {
  $success = dict[];
  $failure = dict[];
  foreach ($traversable as $key => $value) {
    if ($predicate($key, $value)) {
      $success[$key] = $value;
    } else {
      $failure[$key] = $value;
    }
  }
  return tuple($success, $failure);
}
