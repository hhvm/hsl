<?hh // strict
/*
 *  Copyright (c) 2004-present, Facebook, Inc.
 *  All rights reserved.
 *
 *  This source code is licensed under the MIT license found in the
 *  LICENSE file in the root directory of this source tree.
 *
 */

// @oss-disable: <<Oncalls('hack')>>
final class HackLibTestStringable {
  public function __construct(
    private string $data,
  ) {
  }

  public function __toString(): string {
    return $this->data;
  }
}
