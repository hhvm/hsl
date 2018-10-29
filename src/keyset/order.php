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
 * Returns a new keyset sorted by the values of the given Traversable. If the
 * optional comparator function isn't provided, the values will be sorted in
 * ascending order.
 */
<<__Rx, __AtMostRxAsArgs>>
function sort<Tv as arraykey>(
  <<__MaybeMutable, __OnlyRxIfImpl(\HH\Rx\Traversable::class)>>
  Traversable<Tv> $traversable,
  <<__AtMostRxAsFunc>>
  ?(function(Tv, Tv): int) $comparator = null,
): keyset<Tv> {
  $keyset = keyset($traversable);
  if ($comparator) {
    /* HH_FIXME[4200] is reactive */
    /* HH_FIXME[2088] No refs in reactive code. */
    /* HH_FIXME[2049] We are allowed to use PHP Stardard library functions */
    /* HH_FIXME[4107] We are allowed to use PHP Stardard library functions */
    \uksort(&$keyset, $comparator);
  } else {
    /* HH_FIXME[4200] is reactive */
    /* HH_FIXME[2088] No refs in reactive code. */
    \ksort(&$keyset);
  }
  return $keyset;
}
