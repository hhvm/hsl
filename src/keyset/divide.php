<?hh // strict
/*
 *  Copyright (c) 2004-present, Facebook, Inc.
 *  All rights reserved.
 *
 *  This source code is licensed under the MIT license found in the
 *  LICENSE file in the root directory of this source tree.
 *
 */

namespace HH\Lib\Keyset;

/**
 * Returns a 2-tuple containing keysets for which the given predicate returned
 * `true` and `false`, respectively.
 */
function partition<Tv as arraykey>(
  Traversable<Tv> $traversable,
  (function(Tv): bool) $predicate,
): (keyset<Tv>, keyset<Tv>) {
  $success = keyset[];
  $failure = keyset[];
  foreach ($traversable as $value) {
    if ($predicate($value)) {
      $success[] = $value;
    } else {
      $failure[] = $value;
    }
  }
  return tuple($success, $failure);
}
