<?hh // strict
/*
 *  Copyright (c) 2004-present, Facebook, Inc.
 *  All rights reserved.
 *
 *  This source code is licensed under the MIT license found in the
 *  LICENSE file in the root directory of this source tree.
 *
 */

/**
 * @emails oncall+hack
 */

use namespace HH\Lib\SecureRandom;
use type Facebook\HackTest\HackTestCase; // @oss-enable

final class SecureRandomTest extends HackTestCase {
  use RandomTestTrait;

  public function getRandomBool(int $rate): bool {
    return SecureRandom\bool($rate);
  }

  public function getRandomFloat(): float {
    return SecureRandom\float();
  }

  public function getRandomInt(int $min, int $max): int {
    return SecureRandom\int($min, $max);
  }

  public function getRandomString(
    int $length,
    ?string $alphabet = null,
  ): string {
    return SecureRandom\string($length, $alphabet);
  }
}
