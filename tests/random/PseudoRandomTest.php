<?hh // strict
/*
 *  Copyright (c) 2004-present, Facebook, Inc.
 *  All rights reserved.
 *
 *  This source code is licensed under the MIT license found in the
 *  LICENSE file in the root directory of this source tree.
 *
 */


use namespace HH\Lib\PseudoRandom;
use function Facebook\FBExpect\expect;
use type Facebook\HackTest\HackTest; // @oss-enable

// @oss-disable: <<Oncalls('hack')>>
final class PseudoRandomTest extends HackTest {
  use RandomTestTrait;

  public function getRandomFloat(): float {
    return PseudoRandom\float();
  }

  public function getRandomInt(int $min, int $max): int {
    return PseudoRandom\int($min, $max);
  }

  public function getRandomString(
    int $length,
    ?string $alphabet = null,
  ): string {
    return PseudoRandom\string($length, $alphabet);
  }
}
