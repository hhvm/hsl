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

/**
 * Returns a string formed by joining the elements of the Traversable with the
 * given `$glue` string.
 *
 * Previously known as `implode` in PHP.
 */
<<__Rx, __AtMostRxAsArgs>>
function join(
  <<__MaybeMutable, __OnlyRxIfImpl(\HH\Rx\Traversable::class)>>
  Traversable<arraykey> $pieces,
  string $glue,
): string {
  if ($pieces is Container<_>) {
    /* HH_IGNORE_ERROR[2049] __PHPStdLib */
    /* HH_IGNORE_ERROR[4107] __PHPStdLib */
    return \implode($glue, $pieces);
  }
  /* HH_IGNORE_ERROR[2049] __PHPStdLib */
  /* HH_IGNORE_ERROR[4107] __PHPStdLib */
  return \implode($glue, vec($pieces));
}
