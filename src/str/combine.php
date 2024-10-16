<?hh
/*
 *  Copyright (c) 2004-present, Facebook, Inc.
 *  All rights reserved.
 *
 *  This source code is licensed under the MIT license found in the
 *  LICENSE file in the hphp/hsl/ subdirectory of this source tree.
 *
 */

namespace HH\Lib\Str;

use namespace HH\Lib\Vec;

/**
 * Returns a string formed by joining the elements of the Traversable with the
 * given `$glue` string.
 *
 * Previously known as `implode` in PHP.
 *
 * @guide /hack/built-in-types/string
 */
function join(
  readonly Traversable<arraykey> $pieces,
  string $glue,
)[]: string {
  $out = "";
  $first = true;
  foreach ($pieces as $item) {
    if ($first) {
      $out = (string)$item;
      $first = false;
    } else {
      $out .= $glue.(string)$item;
    }
  }
  return $out;
}
