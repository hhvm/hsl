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
use type Facebook\HackTest\HackTestCase; // @oss-enable

<<Oncalls('hack')>>
final class PseudoRandomTest extends HackTestCase {
  use RandomTestTrait;

  public function getRandomBool(int $rate): bool {
    return PseudoRandom\bool($rate);
  }

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
