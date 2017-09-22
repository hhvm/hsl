<?hh //strict
/*
 *  Copyright (c) 2004-present, Facebook, Inc.
 *  All rights reserved.
 *
 *  This source code is licensed under the BSD-style license found in the
 *  LICENSE file in the root directory of this source tree. An additional grant
 *  of patent rights can be found in the PATENTS file in the same directory.
 *
 */

namespace HH\Lib;

use function Facebook\FBExpect\expect;

final class AsyncTestCase extends __Private\TestCase {
  public function testNonAsync(): string {
    // @oss-disable: $this->markTestSkipped('oss-only');
    return __FUNCTION__;
  }

  public async function testAsync(): Awaitable<string> {
    // @oss-disable: $this->markTestSkipped('oss-only');
    await \HH\Asio\later();
    await \HH\Asio\later();
    return __FUNCTION__;
  }

  public function testWrapping(): void {
    // @oss-disable: $this->markTestSkipped('oss-only');
    expect($this->testNonAsync())->toBeSame('testNonAsync');
    expect($this->testAsync())->toBeSame('testAsync');
  }
}
