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
 * Returns a new keyset sorted by the values of the given Traversable. If the
 * optional comparator function isn't provided, the values will be sorted in
 * ascending order.
 *
 * TODO(#13270869) Accept Traversable when keyset() correctly handles Iterators.
 */
function sort<Tv as arraykey>(
  Container<Tv> $traversable,
  ?(function(Tv, Tv): int) $comparator = null,
): keyset<Tv> {
  $keyset = keyset($traversable);
  if ($comparator) {
    \uksort($keyset, $comparator);
  } else {
    \ksort($keyset);
  }
  return $keyset;
}
