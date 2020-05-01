<?hh // strict
/*
 *  Copyright (c) 2004-present, Facebook, Inc.
 *  All rights reserved.
 *
 *  This source code is licensed under the MIT license found in the
 *  LICENSE file in the root directory of this source tree.
 *
 */

namespace HH\Lib\C;

use namespace HH\Lib\{_Private, Str};

/**
 * Returns the first value of the given Traversable for which the predicate
 * returns true, or null if no such value is found.
 *
 * Time complexity: O(n)
 * Space complexity: O(1)
 *
 * @see `C\findx` when a value is required
 */
<<__Rx, __AtMostRxAsArgs>>
function find<T>(
  <<__MaybeMutable, __OnlyRxIfImpl(\HH\Rx\Traversable::class)>>
  Traversable<T> $traversable,
  <<__AtMostRxAsFunc>>
  (function(T): bool) $value_predicate,
): ?T {
  foreach ($traversable as $value) {
    if ($value_predicate($value)) {
      return $value;
    }
  }
  return null;
}

/**
 * Returns the first value of the given Traversable for which the predicate
 * returns true, or throws if no such value is found.
 *
 * Time complexity: O(n)
 * Space complexity: O(1)
 *
 * @see `C\find()` if you would prefer null if not found.
 */
<<__Rx, __AtMostRxAsArgs>>
function findx<T>(
  <<__MaybeMutable, __OnlyRxIfImpl(\HH\Rx\Traversable::class)>>
  Traversable<T> $traversable,
  <<__AtMostRxAsFunc>> (function(T): bool) $value_predicate,
): T {
  foreach ($traversable as $value) {
    if ($value_predicate($value)) {
      return $value;
    }
  }
  invariant_violation('%s: Couldn\'t find target value.', __FUNCTION__);
}

/**
 * Returns the key of the first value of the given KeyedTraversable for which
 * the predicate returns true, or null if no such value is found.
 *
 * Time complexity: O(n)
 * Space complexity: O(1)
 */
<<__Rx, __AtMostRxAsArgs>>
function find_key<Tk, Tv>(
  <<__MaybeMutable, __OnlyRxIfImpl(\HH\Rx\KeyedTraversable::class)>>
  KeyedTraversable<Tk, Tv> $traversable,
  <<__AtMostRxAsFunc>>
  (function(Tv): bool) $value_predicate,
): ?Tk {
  foreach ($traversable as $key => $value) {
    if ($value_predicate($value)) {
      return $key;
    }
  }
  return null;
}

/**
 * Returns the first element of the given Traversable, or null if the
 * Traversable is empty.
 *
 * - For non-empty Traversables, see `C\firstx`.
 * - For possibly null Traversables, see `C\nfirst`.
 * - For single-element Traversables, see `C\onlyx`.
 * - For Awaitables that yield Traversables, see `C\first_async`.
 *
 * Time complexity: O(1)
 * Space complexity: O(1)
 */
<<__Rx, __AtMostRxAsArgs>>
function first<T>(
  <<__MaybeMutable, __OnlyRxIfImpl(\HH\Rx\Traversable::class)>>
  Traversable<T> $traversable,
): ?T {
  if ($traversable is Container<_>) {
    return _Private\Native\first($traversable);
  }
  foreach ($traversable as $value) {
    return $value;
  }
  return null;
}

/**
 * Returns the first element of the given Traversable, or throws if the
 * Traversable is empty.
 *
 * - For possibly empty Traversables, see `C\first`.
 * - For possibly null Traversables, see `C\nfirst`.
 * - For single-element Traversables, see `C\onlyx`.
 * - For Awaitables that yield Traversables, see `C\firstx_async`.
 *
 * Time complexity: O(1)
 * Space complexity: O(1)
 */
<<__Rx, __AtMostRxAsArgs>>
function firstx<T>(
  <<__MaybeMutable, __OnlyRxIfImpl(\HH\Rx\Traversable::class)>>
  Traversable<T> $traversable,
): T {
  if ($traversable is Container<_>) {
    $first_value = _Private\Native\first($traversable);
    if ($first_value is nonnull) {
      return $first_value;
    }
    invariant(
      !is_empty($traversable),
      '%s: Expected at least one element.',
      __FUNCTION__,
    );
    /* HH_IGNORE_ERROR[4110] invariant above implies this is T */
    return $first_value;
  }
  foreach ($traversable as $value) {
    return $value;
  }
  invariant_violation('%s: Expected at least one element.', __FUNCTION__);
}

/**
 * Returns the first key of the given KeyedTraversable, or null if the
 * KeyedTraversable is empty.
 *
 * For non-empty Traversables, see `C\first_keyx`.
 *
 * Time complexity: O(1)
 * Space complexity: O(1)
 */
<<__Rx, __AtMostRxAsArgs>>
function first_key<Tk, Tv>(
  <<__MaybeMutable, __OnlyRxIfImpl(\HH\Rx\KeyedTraversable::class)>>
  KeyedTraversable<Tk, Tv> $traversable,
): ?Tk {
  if ($traversable is KeyedContainer<_, _>) {
    return _Private\Native\first_key($traversable);
  }
  foreach ($traversable as $key => $_) {
    return $key;
  }
  return null;
}

/**
 * Returns the first key of the given KeyedTraversable, or throws if the
 * KeyedTraversable is empty.
 *
 * For possibly empty Traversables, see `C\first_key`.
 *
 * Time complexity: O(1)
 * Space complexity: O(1)
 */
<<__Rx, __AtMostRxAsArgs>>
function first_keyx<Tk, Tv>(
  <<__MaybeMutable, __OnlyRxIfImpl(\HH\Rx\KeyedTraversable::class)>>
  KeyedTraversable<Tk, Tv> $traversable,
): Tk {
  if ($traversable is KeyedContainer<_, _>) {
    $first_key = _Private\Native\first_key($traversable);
    invariant(
      $first_key is nonnull,
      '%s: Expected at least one element.',
      __FUNCTION__,
    );
    return $first_key;
  }
  foreach ($traversable as $key => $_) {
    return $key;
  }
  invariant_violation('%s: Expected at least one element.', __FUNCTION__);
}

/**
 * Returns the last element of the given Traversable, or null if the
 * Traversable is empty.
 *
 * - For non-empty Traversables, see `C\lastx`.
 * - For single-element Traversables, see `C\onlyx`.
 *
 * Time complexity: O(1) if `$traversable` is a `Container`, O(n) otherwise.
 * Space complexity: O(1)
 */
<<__Rx, __AtMostRxAsArgs>>
function last<T>(
  <<__MaybeMutable, __OnlyRxIfImpl(\HH\Rx\Traversable::class)>>
  Traversable<T> $traversable,
): ?T {
  if ($traversable is Container<_>) {
    return _Private\Native\last($traversable);
  }
  if ($traversable is Iterable<_>) {
    /* HH_FIXME[4200] intersection of Iterable and Rx\Traversable is reactive */
    return $traversable->lastValue();
  }
  $value = null;
  foreach ($traversable as $value) {
  }
  return $value;
}

/**
 * Returns the last element of the given Traversable, or throws if the
 * Traversable is empty.
 *
 * - For possibly empty Traversables, see `C\last`.
 * - For single-element Traversables, see `C\onlyx`.
 *
 * Time complexity: O(1) if `$traversable` is a `Container`, O(n) otherwise.
 * Space complexity: O(1)
 */
<<__Rx, __AtMostRxAsArgs>>
function lastx<T>(
  <<__MaybeMutable, __OnlyRxIfImpl(\HH\Rx\Traversable::class)>>
  Traversable<T> $traversable,
): T {
  if ($traversable is Container<_>) {
    $last_value = _Private\Native\last($traversable);
    if ($last_value is nonnull) {
      return $last_value;
    }
    invariant(
      !is_empty($traversable),
      '%s: Expected at least one element.',
      __FUNCTION__,
    );
    /* HH_IGNORE_ERROR[4110] invariant above implies this is T */
    return $last_value;
  }
  $value = null;
  $did_iterate = false;
  foreach ($traversable as $value) {
    $did_iterate = true;
  }
  invariant($did_iterate, '%s: Expected at least one element.', __FUNCTION__);
  /* HH_IGNORE_ERROR[4110] invariant above implies this is T */
  return $value;
}

/**
 * Returns the last key of the given KeyedTraversable, or null if the
 * KeyedTraversable is empty.
 *
 * For non-empty Traversables, see `C\last_keyx`.
 *
 * Time complexity: O(1) if `$traversable` is a `Container`, O(n) otherwise.
 * Space complexity: O(1)
 */
<<__Rx, __AtMostRxAsArgs>>
function last_key<Tk, Tv>(
  <<__MaybeMutable, __OnlyRxIfImpl(\HH\Rx\KeyedTraversable::class)>>
  KeyedTraversable<Tk, Tv> $traversable,
): ?Tk {
  if ($traversable is KeyedContainer<_, _>) {
    return _Private\Native\last_key($traversable);
  }
  if ($traversable is KeyedIterable<_, _>) {
    /* HH_FIXME[4200] intersection of Iterable and Rx\Traversable is reactive */
    return $traversable->lastKey();
  }
  $key = null;
  foreach ($traversable as $key => $_) {
  }
  return $key;
}

/**
 * Returns the last key of the given KeyedTraversable, or throws if the
 * KeyedTraversable is empty.
 *
 * For possibly empty Traversables, see `C\last_key`.
 *
 * Time complexity: O(1) if `$traversable` is a `Container`, O(n) otherwise.
 * Space complexity: O(1)
 */
<<__Rx, __AtMostRxAsArgs>>
function last_keyx<Tk, Tv>(
  <<__MaybeMutable, __OnlyRxIfImpl(\HH\Rx\KeyedTraversable::class)>>
  KeyedTraversable<Tk, Tv> $traversable,
): Tk {
  if ($traversable is KeyedContainer<_, _>) {
    $last_key = _Private\Native\last_key($traversable);
    invariant(
      $last_key is nonnull,
      '%s: Expected at least one element.',
      __FUNCTION__,
    );
    return $last_key;
  }
  $key = null;
  $did_iterate = false;
  foreach ($traversable as $key => $_) {
    $did_iterate = true;
  }
  invariant($did_iterate, '%s: Expected at least one element.', __FUNCTION__);
  /* HH_IGNORE_ERROR[4110] invariant above implies this is Tk */
  return $key;
}

/**
 * Returns the first element of the given Traversable, or null if the
 * Traversable is null or empty.
 *
 * - For non-null Traversables, see `C\first`.
 * - For non-empty Traversables, see `C\firstx`.
 * - For single-element Traversables, see `C\onlyx`.
 *
 * Time complexity: O(1)
 * Space complexity: O(1)
 */
<<__Rx, __AtMostRxAsArgs>>
function nfirst<T>(
  <<__OnlyRxIfImpl(\HH\Rx\Traversable::class)>>
  ?Traversable<T> $traversable,
): ?T {
  return $traversable is nonnull ? first($traversable) : null;
}

/**
 * Returns the first and only element of the given Traversable, or throws if the
 * Traversable is empty.
 *
 * An optional format string (and format arguments) may be passed to specify
 * a custom message for the exception in the error case.
 *
 * For Traversables with more than one element, see `C\firstx`.
 *
 * Time complexity: O(1)
 * Space complexity: O(1)
 */
<<__Rx, __AtMostRxAsArgs>>
function onlyx<T>(
  <<__MaybeMutable, __OnlyRxIfImpl(\HH\Rx\Traversable::class)>>
  Traversable<T> $traversable,
  ?Str\SprintfFormatString $format_string = null,
  mixed ...$format_args
): T {
  $first = true;
  $result = null;
  foreach ($traversable as $value) {
    invariant(
      $first,
      '%s',
      $format_string === null
        ? Str\format(
          'Expected exactly one element%s.',
          $traversable is Container<_>
            ? ' but got '.count($traversable)
            : '',
        )
        /* HH_IGNORE_ERROR[2049] __PHPStdLib */
        /* HH_IGNORE_ERROR[4107] __PHPStdLib */
        /* HH_IGNORE_ERROR[4200] __PHPStdLib */
        : \vsprintf($format_string, $format_args),
    );
    $result = $value;
    $first = false;
  }
  invariant(
    $first === false,
    '%s',
    $format_string === null
      ? 'Expected non-empty Traversable.'
      /* HH_IGNORE_ERROR[2049] __PHPStdLib */
      /* HH_IGNORE_ERROR[4107] __PHPStdLib */
      /* HH_IGNORE_ERROR[4200] __PHPStdLib */
      : \vsprintf($format_string, $format_args),
  );
  /* HH_IGNORE_ERROR[4110] $first is false implies $result is set to T */
  return $result;
}
