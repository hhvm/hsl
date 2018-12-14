<?hh // strict
/*
 *  Copyright (c) 2004-present, Facebook, Inc.
 *  All rights reserved.
 *
 *  This source code is licensed under the MIT license found in the
 *  LICENSE file in the root directory of this source tree.
 *
 */

namespace HH\Lib\Str;

use namespace HH\Lib\Str;

/**
 * Returns a vec containing the string split into chunks of the given size.
 *
 * To split the string on a delimiter, see `Str\split()`.
 */
<<__Rx>>
function chunk(
  string $string,
  int $chunk_size = 1,
): vec<string> {
  invariant($chunk_size >= 1, 'Expected positive chunk size.');
  /* HH_FIXME[2049] We are allowed to use PHP Stardard library functions */
  /* HH_FIXME[4107] We are allowed to use PHP Stardard library functions */
  return vec(\str_split($string, $chunk_size));
}

/**
 * Returns a vec containing the string split on the given delimiter. The vec
 * will not contain the delimiter itself.
 *
 * If the limit is provided, the vec will only contain that many elements, where
 * the last element is the remainder of the string.
 *
 * To split the string into equally-sized chunks, see `Str\chunk()`.
 *
 * Previously known as `explode` in PHP.
 */
<<__Rx>>
function split(
  string $string,
  string $delimiter,
  ?int $limit = null,
): vec<string> {
  if ($delimiter === '') {
    if ($limit === null || $limit >= Str\length($string)) {
      return chunk($string);
    } else if ($limit === 1) {
      return vec[$string];
    } else {
      invariant($limit > 1, 'Expected positive limit.');
      $result = chunk(\substr($string, 0, $limit - 1));
      $result[] = \substr($string, $limit - 1);
      return $result;
    }
  } else if ($limit === null) {
    /* HH_IGNORE_ERROR[2049] __PHPStdLib */
    /* HH_IGNORE_ERROR[4107] __PHPStdLib */
    return vec(\explode($delimiter, $string));
  } else {
    /* HH_IGNORE_ERROR[2049] __PHPStdLib */
    /* HH_IGNORE_ERROR[4107] __PHPStdLib */
    return vec(\explode($delimiter, $string, $limit));
  }
}
