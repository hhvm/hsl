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

const string ALPHABET_ALPHANUMERIC =
  '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
