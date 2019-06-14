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

use namespace HH\Lib\{Regex, Str};

/**
 * Temporary stand-in for native match function to be implemented in T30991246.
 * Returns the first match found in `$haystack` given the regex pattern `$pattern`
 * and an optional offset at which to start the search.
 *
 * Throws Invariant[Violation]Exception if `$offset` is not within plus/minus the length of `$haystack`
 * Returns null, or a tuple of
 * first,
 *   a Match containing
 *     - the entire matching string, at key 0,
 *     - the results of unnamed capture groups, at integer keys corresponding to
 *         the groups' occurrence within the pattern, and
 *     - the results of named capture groups, at string keys matching their respective names,
 * and second,
 *   the integer offset at which this first match occurs in the haystack string.
 */
function regex_match<T as Regex\Match>(
  string $haystack,
  Regex\Pattern<T> $pattern,
  int $offset = 0,
): ?(T, int) {
  $offset = validate_offset($offset, Str\length($haystack));
  $match = darray[];
  /* HH_IGNORE_ERROR[2049] __PHPStdLib */
  /* HH_IGNORE_ERROR[4107] __PHPStdLib */
  $status = @\preg_match(
    /* HH_IGNORE_ERROR[4110] */ $pattern,
    $haystack,
    &$match,
    /* HH_IGNORE_ERROR[2049] Private constant */
    /* HH_IGNORE_ERROR[4106] Private constant */
    \PREG_FB__PRIVATE__HSL_IMPL | \PREG_OFFSET_CAPTURE,
    $offset,
  );
  if ($status === 1) {
    $match_out = darray[];
    foreach ($match as $key => $value) {
      $match_out[$key] = $value[0];
    }
    $offset_out = $match[0][1];
    /* HH_FIXME[4110] Native function won't have this problem */
    return tuple($match_out, $offset_out);
  } else if ($status === 0) {
    return null;
  } else {
    throw new Regex\Exception($pattern);
  }
}
