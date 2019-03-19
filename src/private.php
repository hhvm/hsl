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
 * Returns whether the input is either a PHP array or Hack array.
 */
<<__Rx>>
function is_any_array(<<__MaybeMutable>> mixed $val): bool {
  return $val is dict<_, _> || $val is vec<_> || $val is keyset<_> || \is_array($val);
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

const dict<string, int> INVERT_ALPHABET_ALPHANUMERIC_CI = dict[
  "0" => 0, "1" => 1, "2" => 2, "3" => 3, "4" => 4,
  "5" => 5, "6" => 6, "7" => 7, "8" => 8, "9" => 9,
  "a" => 10, "b" => 11, "c" => 12, "d" => 13, "e" => 14, "f" => 15, "g" => 16,
  "h" => 17, "i" => 18, "j" => 19, "k" => 20, "l" => 21, "m" => 22, "n" => 23,
  "o" => 24, "p" => 25, "q" => 26, "r" => 27, "s" => 28, "t" => 29, "u" => 30,
  "v" => 31, "w" => 32, "x" => 33, "y" => 34, "z" => 35,
  "A" => 10, "B" => 11, "C" => 12, "D" => 13, "E" => 14, "F" => 15, "G" => 16,
  "H" => 17, "I" => 18, "J" => 19, "K" => 20, "L" => 21, "M" => 22, "N" => 23,
  "O" => 24, "P" => 25, "Q" => 26, "R" => 27, "S" => 28, "T" => 29, "U" => 30,
  "V" => 31, "W" => 32, "X" => 33, "Y" => 34, "Z" => 35,
];
const vec<int> INVERT_ALPHABET_ALPHANUMERIC_CI_VEC = vec[
  99, 99, 99, 99, 99, 99, 99, 99,  99, 99, 99, 99, 99, 99, 99, 99,
  99, 99, 99, 99, 99, 99, 99, 99,  99, 99, 99, 99, 99, 99, 99, 99,
  99, 99, 99, 99, 99, 99, 99, 99,  99, 99, 99, 99, 99, 99, 99, 99,
   0,  1,  2,  3,  4,  5,  6,  7,   8,  9, 99, 99, 99, 99, 99, 99,
  99, 10, 11, 12, 13, 14, 15, 16,  17, 18, 19, 20, 21, 22, 23, 24,
  25, 26, 27, 28, 29, 30, 31, 32,  33, 34, 35, 99, 99, 99, 99, 99,
  99, 10, 11, 12, 13, 14, 15, 16,  17, 18, 19, 20, 21, 22, 23, 24,
  25, 26, 27, 28, 29, 30, 31, 32,  33, 34, 35, 99, 99, 99, 99, 99,

  99, 99, 99, 99, 99, 99, 99, 99,  99, 99, 99, 99, 99, 99, 99, 99,
  99, 99, 99, 99, 99, 99, 99, 99,  99, 99, 99, 99, 99, 99, 99, 99,
  99, 99, 99, 99, 99, 99, 99, 99,  99, 99, 99, 99, 99, 99, 99, 99,
  99, 99, 99, 99, 99, 99, 99, 99,  99, 99, 99, 99, 99, 99, 99, 99,
  99, 99, 99, 99, 99, 99, 99, 99,  99, 99, 99, 99, 99, 99, 99, 99,
  99, 99, 99, 99, 99, 99, 99, 99,  99, 99, 99, 99, 99, 99, 99, 99,
  99, 99, 99, 99, 99, 99, 99, 99,  99, 99, 99, 99, 99, 99, 99, 99,
  99, 99, 99, 99, 99, 99, 99, 99,  99, 99, 99, 99, 99, 99, 99, 99,
];

final class Ref<T> {
  <<__RxShallow>>
  public function __construct(public T $value) {}
}

async function wAsync<T>(
  Awaitable<T> $gen,
): Awaitable<ResultOrExceptionWrapper<T>> {
  try {
    $result = await $gen;
    return new WrappedResult($result);
  } catch (\Exception $e) {
    return new WrappedException($e);
  }
}

abstract class ResultOrExceptionWrapper<+T> {
  abstract public function get(): T;
}

final class WrappedResult<T>
  extends ResultOrExceptionWrapper<T> {
  <<__Rx>>
  public function __construct(private T $value) {}
  <<__Override, __Rx>>
  public function get(): T {
    return $this->value;
  }
}

final class WrappedException<Te as \Exception, Tr>
  extends ResultOrExceptionWrapper<Tr> {

  <<__Rx>>
  public function __construct(private Te $exception) {}

  <<__Override, __Rx>>
  public function get(): Tr {
    throw $this->exception;
  }
}
