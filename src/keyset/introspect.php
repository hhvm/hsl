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

namespace HH\Lib\Keyset;

use namespace \HH\Lib\C;

/**
 * Returns whether the two given keysets have the same elements, using strict
 * equality. To guarantee equality of order as well as contents, use `===`.
 */
function equal<Tv as arraykey>(
  keyset<Tv> $keyset1,
  keyset<Tv> $keyset2,
): bool {
  return $keyset1 == $keyset2;
}
