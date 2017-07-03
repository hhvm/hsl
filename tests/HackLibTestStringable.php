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

/**
 * @emails oncall+hack_prod_infra
 */
final class HackLibTestStringable {
  public function __construct(
    private string $data,
  ) {
  }

  public function __toString(): string {
    return $this->data;
  }
}
