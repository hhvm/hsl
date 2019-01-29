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
