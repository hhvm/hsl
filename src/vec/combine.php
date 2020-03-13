<?hh // strict
/*
 *  Copyright (c) 2004-present, Facebook, Inc.
 *  All rights reserved.
 *
 *  This source code is licensed under the MIT license found in the
 *  LICENSE file in the root directory of this source tree.
 *
 */

namespace HH\Lib\Vec;

use namespace HH\Lib\{C, Math};

/**
 * Returns a new vec formed by concatenating the given Traversables together.
 *
 * For a variable number of Traversables, see `Vec\flatten()`.
 *
 * Time complexity: O(n + m), where n is the size of `$first` and m is the
 * combined size of all the `...$rest`
 * Space complexity: O(n + m), where n is the size of `$first` and m is the
 * combined size of all the `...$rest`
 */
<<__Rx, __AtMostRxAsArgs>>
function concat<Tv>(
  <<__OnlyRxIfImpl(\HH\Rx\Traversable::class)>> Traversable<Tv> $first,
  Container<Tv> ...$rest
): vec<Tv> {
  $result = vec($first);
  foreach ($rest as $traversable) {
    foreach ($traversable as $value) {
      $result[] = $value;
    }
  }
  return $result;
}

/**
 * Returns a vec where each element is a tuple (pair) that combines, pairwise,
 * the elements of the two given Traversables.
 *
 * If the Traversables are not of equal length, the result will have
 * the same number of elements as the shortest Traversable.
 * Elements of the longer Traversable after the length of the shorter one
 * will be ignored.
 *
 * Time complexity: O(min(m, n)), where m is the size of `$first` and n is the
 * size of `$second`
 * Space complexity: O(min(m, n)), where m is the size of `$first` and n is the
 * size of `$second`
 */
<<__Rx, __AtMostRxAsArgs>>
function zip<Tv, Tu>(
  <<__MaybeMutable, __OnlyRxIfImpl(\HH\Rx\Traversable::class)>>
  Traversable<Tv> $first,
  <<__MaybeMutable, __OnlyRxIfImpl(\HH\Rx\Traversable::class)>>
  Traversable<Tu> $second,
): vec<(Tv, Tu)> {
  $one = vec($first);
  $two = vec($second);
  $result = vec[];
  $lesser_count = Math\minva(C\count($one), C\count($two));
  for ($i = 0; $i < $lesser_count; ++$i) {
    $result[] = tuple($one[$i], $two[$i]);
  }
  return $result;
}
