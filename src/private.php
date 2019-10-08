<?hh // strict
/*
 *  Copyright (c) 2004-present, Facebook, Inc.
 *  All rights reserved.
 *
 *  This source code is licensed under the MIT license found in the
 *  LICENSE file in the root directory of this source tree.
 *
 */

namespace HH\Lib\_Private;

/**
 * Verifies that the `$offset` is within plus/minus `$length`. Returns the
 * offset as a positive integer.
 */
<<__Rx>>
function validate_offset(
  int $offset,
  int $length,
): int {
  $original_offset = $offset;
  if ($offset < 0) {
    $offset += $length;
  }
  invariant(
    $offset >= 0 && $offset <= $length,
    'Offset (%d) was out-of-bounds.',
    $original_offset,
  );
  return $offset;
}

/**
 * Verifies that the `$offset` is not less than minus `$length`. Returns the
 * offset as a positive integer.
 */
<<__Rx>>
function validate_offset_lower_bound(
  int $offset,
  int $length,
): int {
  $original_offset = $offset;
  if ($offset < 0) {
    $offset += $length;
  }
  invariant($offset >= 0, 'Offset (%d) was out-of-bounds.', $original_offset);
  return $offset;
}

<<__Rx>>
function boolval(mixed $val): bool {
  return (bool)$val;
}

// Stub implementations of FB internals used to ease migrations

<<__Rx>>
function tuple_from_vec(mixed $x): mixed {
  // @oss-disable: invariant_violation("Use varray instead.");
  return is_vec(tuple(1,2)) // @oss-enable
    ? $x // @oss-enable
    : /* HH_IGNORE_ERROR[4007] */ (array) $x; // @oss-enable
}

const string ALPHABET_ALPHANUMERIC =
  '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
