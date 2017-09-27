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
<<__Deprecated('(TEMPORARY) use Str\\split_args_switched; see docblock')>>
function split(
  string $delimiter,
  string $string,
  ?int $limit = null,
): vec<string> {
  return namespace\split_args_switched($string, $delimiter, $limit);
}

/**
 * Q: What is this gross function?
 * A: Currently, this API is inconsistent with the rest of the library, whose
 * principle is that the "element being operated on" (in this case, the target
 * string) should be the first element. This temporary function will be used
 * to correct that inconsistency.
 *
 * Q: Why are you disrupting my workflow?
 * A: We recognize that this will be disruptive in the short term and apologize
 * for the inconvenience. However, it provides the benefit of predictablity
 * for all future users of the HSL.
 *
 * Q: But why now?
 * We are expecting to mark the HSL as 1.0 soon, so this is our only chance to
 * fix this.
 *
 * Q: Okay, fine... what's the game plan?
 * A: All uses of Str\split will be codemodded to Str\split_args_switched, and
 * Str\split will be temporarily deprecated. After a short period, Str\split
 * will be fixed, all callsites will be codemodded back, and this function will
 * be removed.
 *
 * Task: T17219441
 */
function split_args_switched(
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
