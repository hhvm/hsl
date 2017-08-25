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

// @oss-disable: use \PHPism_FIXME as PHPism_FIXME;
use HH\Lib\_Private\StubPHPism_FIXME as PHPism_FIXME; // @oss-enable
use function HH\Lib\_Private\universal_chainable_stub as FBLogger; // @oss-enable

/**
 * Returns a vec containing the given Traversable split into chunks of the
 * given size.
 *
 * If the given Traversable doesn't divide evenly, the final chunk will be
 * smaller than the specified size. If there are duplicate values in the
 * Traversable, some chunks may be smaller than the specified size.
 */
function chunk<Tv as arraykey>(
  Traversable<Tv> $traversable,
  int $size,
): vec<keyset<Tv>> {
  invariant($size > 0, 'Expected positive chunk size, got %d.', $size);
  $result = vec[];
  $ii = 0;
  foreach ($traversable as $value) {
    if ($ii % $size === 0) {
      $result[] = keyset[];
    }
    $result[\intdiv($ii, $size)][] = $value;
    $ii++;
  }
  return $result;
}

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
    if (!PHPism_FIXME::isForeachable($traversable)) {
      FBLogger('phpism_fixme.invalid_foreach_arg')
        ->blameToPreviousFrame()
        ->mustfix('Attempting to foreach over a non-foreachable type');
      continue;
    }
    foreach ($traversable as $value) {
      $result[] = $value;
    }
  }
  return $result;
}
