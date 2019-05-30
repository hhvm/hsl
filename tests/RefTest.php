<?hh // strict
/*
 *  Copyright (c) 2004-present, Facebook, Inc.
 *  All rights reserved.
 *
 *  This source code is licensed under the MIT license found in the
 *  LICENSE file in the root directory of this source tree.
 *
 */

use function Facebook\FBExpect\expect; // @oss-enable
use type Facebook\HackTest\HackTest; // @oss-enable
// @oss-disable: use InvariantViolationException as InvariantException;

// @oss-disable: <<Oncalls('hf')>>
final class RefTest extends HackTest {
  public function testRefiness(): void {
    $myref = new \HH\Lib\Ref(0);
    // Non-inout arg
    (
      (\HH\Lib\Ref<int> $ref) ==> {
        $ref->value++;
      }
    )($myref);
    expect($myref->value)->toBeSame(1);
    // implicit capture, which is always byval
    (
      () ==> {
        $myref->value++;
      }
    )();
    expect($myref->value)->toBeSame(2);
  }
}
