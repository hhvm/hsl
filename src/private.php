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

namespace HH\Lib\_Private;

/**
 * Verifies that the `$offset` is within plus/minus `$length`. Returns the
 * offset as a positive integer.
 */
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
 * Returns whether the input is either a PHP array or Hack array.
 */
function is_any_array(mixed $val): bool {
  return \is_dict($val) || \is_vec($val) || \is_keyset($val) || \is_array($val);
}

function boolval(mixed $val): bool {
  return (bool)$val;
}

// Stub implementations of FB internals used to ease migrations

/* HH_IGNORE_ERROR[5520] FIXME violates FB naming conventions */
final class StubPHPism_FIXME {
  public static function isForeachable<T>(Traversable<T> $_): bool {
    return true;
  }
}

final class UniversalChainableStub {
  public function __call(mixed $_, mixed $_): this {
    return $this;
  }
}

function universal_chainable_stub(mixed ...$_): UniversalChainableStub {
  return new UniversalChainableStub();
}

function tuple_from_vec(mixed $x): mixed {
  // @oss-disable: invariant_violation("Use varray instead.");
  return is_vec(tuple(1,2)) // @oss-enable
    ? $x // @oss-enable
    : /* HH_IGNORE_ERROR[4007] */ (array) $x; // @oss-enable
}

const string ALPHABET_ALPHANUMERIC =
  '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
