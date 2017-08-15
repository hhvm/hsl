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

namespace HH\Lib\Vec;

use \HH\Lib\Dict;
use \HH\Lib\Keyset;

/**
 * Returns a new vec containing only the elements of the first Traversable that
 * do not appear in any of the other ones.
 *
 * For vecs that contain non-arraykey elements, see Vec\diff_by.
 */
function diff<Tv1 as arraykey, Tv2 as arraykey>(
  Traversable<Tv1> $first,
  Traversable<Tv2> $second,
  Traversable<Tv2> ...$rest
): vec<Tv1> {
  if (!$first) {
    return vec[];
  }
  if (!$second && !$rest) {
    return vec($first);
  }
  $union = !$rest
    ? keyset($second)
    : Keyset\union($second, ...$rest);
  return namespace\filter(
    $first,
    ($value) ==> !\array_key_exists($value, $union),
  );
}

/**
 * Returns a new vec containing only the elements of the first Traversable
 * that do not appear in the second one, where an element's identity is
 * determined by the scalar function.
 *
 * For vecs that contain arraykey elements, see Vec\diff.
 */
function diff_by<Tv, Ts as arraykey>(
  Traversable<Tv> $first,
  Traversable<Tv> $second,
  (function(Tv): Ts) $scalar_func,
): vec<Tv> {
  if (!$first) {
    return vec[];
  }
  if (!$second) {
    return vec($first);
  }
  $set = Keyset\map($second, $scalar_func);
  return namespace\filter(
    $first,
    ($value) ==> !\array_key_exists($scalar_func($value), $set),
  );
}

/**
 * Returns a new vec containing all except the first `$n` elements of the
 * given Traversable.
 *
 * To take only the first `$n` elements, see `Vec\take`.
 */
function drop<Tv>(
  Traversable<Tv> $traversable,
  int $n,
): vec<Tv> {
  invariant($n >= 0, 'Expected non-negative N, got %d.', $n);
  $result = vec[];
  $ii = -1;
  foreach ($traversable as $value) {
    $ii++;
    if ($ii < $n) {
      continue;
    }
    $result[] = $value;
  }
  return $result;
}

/**
 * Returns a new vec containing only the values for which the given predicate
 * returns `true`. The default predicate is casting the value to boolean.
 *
 * To remove null values in a typechecker-visible way, see Vec\filter_nulls.
 * To use an async predicate, see Vec\gen_filter.
 */
function filter<Tv>(
  Traversable<Tv> $traversable,
  ?(function(Tv): bool) $value_predicate = null,
): vec<Tv> {
  $value_predicate = $value_predicate ?? fun('boolval');
  $result = vec[];
  foreach ($traversable as $value) {
    if ($value_predicate($value)) {
      $result[] = $value;
    }
  }
  return $result;
}

/**
 * Returns a new vec containing only non-null values of the given
 * Traversable.
 */
function filter_nulls<Tv>(
  Traversable<?Tv> $traversable,
): vec<Tv> {
  $result = vec[];
  foreach ($traversable as $value) {
    if ($value !== null) {
      $result[] = $value;
    }
  }
  return $result;
}

/**
 * Returns a new vec containing only the elements of the first Traversable that
 * appear in all the other ones. Duplicate values are preserved.
 */
function intersect<Tv as arraykey>(
  Traversable<Tv> $first,
  Traversable<Tv> $second,
  Traversable<Tv> ...$rest
): vec<Tv> {
  $intersection = Keyset\intersect($first, $second, ...$rest);
  if (!$intersection) {
    return vec[];
  }
  return namespace\filter(
    $first,
    ($value) ==> \array_key_exists($value, $intersection),
  );
}

/**
  * Returns a new vec containing the keys of the given KeyedTraversable.
  */
function keys<Tk, Tv>(
  KeyedTraversable<Tk, Tv> $traversable,
): vec<Tk> {
  $result = vec[];
  foreach ($traversable as $key => $_) {
    $result[] = $key;
  }
  return $result;
}

/**
 * Returns a new vec containing only the keys of the given KeyedTraversable
 * that map to truthy values.
 */
<<__Deprecated('use Vec\\fb\\keys_with_truthy_values internally')>>
function keys_with_truthy_values<Tk, Tv>(
  KeyedTraversable<Tk, Tv> $traversable,
): vec<Tk> {
  $result = vec[];
  foreach ($traversable as $key => $value) {
    if ($value) {
      $result[] = $key;
    }
  }
  return $result;
}

/**
 * Returns a new vec containing an unbiased random sample of up to
 * `$sample_size` elements (fewer iff `$sample_size` is larger than the size of
 * `$traversable`).
 */
function sample<Tv>(
  Traversable<Tv> $traversable,
  int $sample_size,
): vec<Tv> {
  invariant(
    $sample_size >= 0,
    'Expected non-negative sample size, got %d.',
    $sample_size,
  );
  return $traversable
    |> namespace\shuffle($$)
    |> namespace\take($$, $sample_size);
}

/**
 * Returns a new vec containing the subsequence of the given Traversable
 * determined by the offset and length.
 *
 * If no length is given or it exceeds the upper bound of the Traversable,
 * the vec will contain every element after the offset.
 *
 * To take only the first `$n` elements, see `Vec\take`.
 * To drop the first `$n` elements, see `Vec\drop`.
 */
function slice<Tv>(
  Container<Tv> $container,
  int $offset,
  ?int $length = null,
): vec<Tv> {
  invariant($offset >= 0, 'Expected non-negative offset.');
  invariant($length === null || $length >= 0, 'Expected non-negative length.');
  return vec(\array_slice($container, $offset, $length));
}

/**
 * Returns a new vec containing the first `$n` elements of the given
 * Traversable.
 *
 * To drop the first `$n` elements, see `Vec\drop`.
 */
function take<Tv>(
  Traversable<Tv> $traversable,
  int $n,
): vec<Tv> {
  if ($n === 0) {
    return vec[];
  }
  invariant($n > 0, 'Expected non-negative N, got %d.', $n);
  $result = vec[];
  $ii = 0;
  foreach ($traversable as $value) {
    $result[] = $value;
    $ii++;
    if ($ii === $n) {
      break;
    }
  }
  return $result;
}

/**
 * Returns a new vec containing each element of the given Traversable exactly
 * once. The Traversable must contain arraykey values, and strict equality will
 * be used.
 *
 * For non-arraykey elements, see Vec\unique_by.
 */
function unique<Tv as arraykey>(
  Traversable<Tv> $traversable,
): vec<Tv> {
  return vec(keyset($traversable));
}

/**
 * Returns a new vec containing each element of the given Traversable exactly
 * once, where uniqueness is determined by calling the given scalar function on
 * the values. In case of duplicate scalar keys, later values will overwrite
 * previous ones.
 *
 * For arraykey elements, see Vec\unique.
 */
function unique_by<Tv, Ts as arraykey>(
  Traversable<Tv> $traversable,
  (function(Tv): Ts) $scalar_func,
): vec<Tv> {
  return vec(Dict\from_values($traversable, $scalar_func));
}
