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

const string ALPHABET_ALPHANUMERIC =
  '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

/**
 * Many PHP builtins emit warnings to stderr when they fail. This
 * class allows us to squash warnings for a time without using PHP's
 * `@` annotation.
 */
final class PHPWarningSuppressor implements \IDisposable {

  private int $warningLevel;

  public function __construct() {
    /* HH_IGNORE_ERROR[2049] __PHPStdLib */
    /* HH_IGNORE_ERROR[4107] __PHPStdLib */
    $this->warningLevel = \error_reporting(0);
  }

  public function __dispose(): void {
    /* HH_IGNORE_ERROR[2049] __PHPStdLib */
    /* HH_IGNORE_ERROR[4107] __PHPStdLib */
    \error_reporting($this->warningLevel);
  }
}

/**
 * Stop eager execution of an async function.
 *
 * ==== ONLY USE THIS IN HSL IMPLEMENTATION AND TESTS ===
 */
function stop_eager_execution(): RescheduleWaitHandle {
  return RescheduleWaitHandle::create(RescheduleWaitHandle::QUEUE_DEFAULT, 0);
}
