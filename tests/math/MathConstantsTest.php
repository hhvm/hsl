<?hh // strict
/*
 *  Copyright (c) 2004-present, Facebook, Inc.
 *  All rights reserved.
 *
 *  This source code is licensed under the MIT license found in the
 *  LICENSE file in the root directory of this source tree.
 *
 */

use namespace HH\Lib\Math;
use function Facebook\FBExpect\expect; // @oss-enable
use type Facebook\HackTest\HackTest; // @oss-enable
// @oss-disable: use InvariantViolationException as InvariantException;

// @oss-disable: <<Oncalls('hack')>>
final class MathConstantsTest extends HackTest {
  public function testInt64Min(): void {
    expect(Math\INT64_MIN)->toBeLessThan(0);
    $less = Math\INT64_MIN- 1;
    expect($less === Math\INT64_MAX || $less is float)->toBeTrue();
  }
}
