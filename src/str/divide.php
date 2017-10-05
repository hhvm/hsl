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

namespace HH\Lib\Str;

/**
 * Returns a vec containing the string split into chunks of the given size.
 */
function chunk(
  string $string,
  int $chunk_size = 1,
): vec<string> {
  invariant($chunk_size >= 1, 'Expected positive chunk size.');
  return vec(\str_split($string, $chunk_size));
}

/**
 * Returns a vec containing the string split on the given delimiter. The vec
 * will not contain the delimiter itself.
 *
 * If the limit is provided, the vec will only contain that many elements, where
 * the last element is the remainder of the string.
 *
 * Previously known as `explode` in PHP.
 */
function split(
  string $string,
  string $delimiter,
  ?int $limit = null,
): vec<string> {
  if ($delimiter === '') {
    if ($limit === null || $limit >= \strlen($string)) {
      return namespace\chunk($string);
    } else if ($limit === 1) {
      return vec[$string];
    } else {
      invariant($limit > 1, 'Expected positive limit.');
      $result = namespace\chunk(\substr($string, 0, $limit - 1));
      $result[] = \substr($string, $limit - 1);
      return $result;
    }
  } else if ($limit === null) {
    return vec(\explode($delimiter, $string));
  } else {
    return vec(\explode($delimiter, $string, $limit));
  }
}
