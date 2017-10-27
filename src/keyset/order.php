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

namespace HH\Lib\Keyset;

/**
 * Returns a new keyset sorted by the values of the given Traversable. If the
 * optional comparator function isn't provided, the values will be sorted in
 * ascending order.
 */
function sort<Tv as arraykey>(
  Traversable<Tv> $traversable,
  ?(function(Tv, Tv): int) $comparator = null,
): keyset<Tv> {
  $keyset = keyset($traversable);
  if ($comparator) {
    \uksort(/*ctpbr:&*/$keyset, $comparator);
  } else {
    \ksort(/*ctpbr:&*/$keyset);
  }
  return $keyset;
}
