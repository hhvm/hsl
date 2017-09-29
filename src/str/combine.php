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
 * Returns a string formed by joining the elements of the Traversable with the
 * given `$glue` string.
 *
 * Previously known as `implode` in PHP.
 */
<<__Deprecated('(TEMPORARY) use Str\\join_args_switched; see docblock')>>
function join(
  string $glue,
  Traversable<arraykey> $pieces,
): string {
  return namespace\join_args_switched($pieces, $glue);
}

/**
 * Q: What is this gross function?
 * A: Currently, this API is inconsistent with the rest of the library, whose
 * principle is that the "element being operated on" (in this case, the target
 * Traversable) should be the first element. This temporary function will be
 * used to correct that inconsistency.
 *
 * Q: Why are you disrupting my workflow?
 * A: We recognize that this will be disruptive in the short term and apologize
 * for the inconvenience. However, it provides the benefit of predictablity
 * for all future users of the HSL.
 *
 * Q: But why now?
 * We are expecting to mark the HSL as stable soon, so this is our only chance
 * to fix this.
 *
 * Q: Okay, fine... what's the game plan?
 * A: All uses of Str\join will be codemodded to Str\join_args_switched, and
 * Str\join will be temporarily deprecated. After a short period, Str\join
 * will be fixed, all callsites will be codemodded back, and this function will
 * be removed.
 *
 * Task: T17219441
 */
function join_args_switched(
  Traversable<arraykey> $pieces,
  string $glue,
): string {
  if (!($pieces instanceof Container)) {
    $pieces = vec($pieces);
  }
  return \implode($glue, $pieces);
}
