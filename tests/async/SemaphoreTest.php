<?hh // strict
/*
 *  Copyright (c) 2004-present, Facebook, Inc.
 *  All rights reserved.
 *
 *  This source code is licensed under the MIT license found in the
 *  LICENSE file in the root directory of this source tree.
 *
 */

use namespace HH\Lib\Vec;
use namespace HH\Lib\Async;
use type HH\Lib\Ref;

use function Facebook\FBExpect\expect; // @oss-enable
use type Facebook\HackTest\HackTest; // @oss-enable
// @oss-disable: use type HackTest;
use function HH\Lib\_Private\stop_eager_execution;

// @oss-disable: <<
  // @oss-disable: Oncalls('hphp_hphpi')
// @oss-disable: >>
final class SemaphoreTest extends HackTest {

  const float TIME_DELTA = 1.;
  const int USLEEP_BLOCK = 500000;
  const float SLEEP_BLOCK = self::USLEEP_BLOCK / 1000000.;

  public async function testLimitConcurrencySimpleAsync(): Awaitable<void> {
    $semaphore = new Async\Semaphore(10, async ($i) ==> {
      await HH\Asio\usleep(self::USLEEP_BLOCK);
      return $i;
    });
    /* HH_IGNORE_ERROR[4107] PHP stdlib */
    /* HH_IGNORE_ERROR[2049] PHP stdlib */
    $start = microtime(true);
    $results = await Vec\map_async(
      Vec\range(0, 99),
      async $i ==> await $semaphore->waitForAsync($i),
    );
    /* HH_IGNORE_ERROR[4107] PHP stdlib */
    /* HH_IGNORE_ERROR[2049] PHP stdlib */
    $end = microtime(true);
    expect($end - $start)->toEqualWithDelta(self::SLEEP_BLOCK * 10, self::TIME_DELTA);
    expect($results)->toEqual(Vec\range(0, 99));
  }

  public async function testLimitConcurrencyFastAsync(): Awaitable<void> {
    $semaphore = new Async\Semaphore(10, async ($i) ==> $i);
    $results = await Vec\map_async(
      Vec\range(0, 99),
      async $i ==> await $semaphore->waitForAsync($i),
    );
    expect($results)->toEqual(Vec\range(0, 99));
  }

  public async function testConcurrencyLimiterExceptionAsync(): Awaitable<void> {
    $semaphore = new Async\Semaphore(10, async ($_) ==> {
      await HH\Asio\usleep(self::USLEEP_BLOCK);
      throw new \Exception();
    });
    /* HH_IGNORE_ERROR[4107] PHP stdlib */
    /* HH_IGNORE_ERROR[2049] PHP stdlib */
    $start = microtime(true);
    await Vec\map_async(
      Vec\range(0, 99),
      async $_ ==> await HH\Asio\wrap($semaphore->waitForAsync(42)),
    );
    /* HH_IGNORE_ERROR[4107] PHP stdlib */
    /* HH_IGNORE_ERROR[2049] PHP stdlib */
    $end = microtime(true);
    expect($end - $start)->toEqualWithDelta(self::SLEEP_BLOCK * 10, self::TIME_DELTA);
  }

  public async function testConcurrencyLimiterSingleAsync(): Awaitable<void> {
    $checker = new Ref(false);
    $semaphore = new Async\Semaphore(1, async ($i) ==> {
      expect($checker->value)
        ->toBeFalse('Found two running at the same time');
      $checker->value = true;
      await HH\Asio\usleep(self::USLEEP_BLOCK);
      expect($checker->value)->toBeTrue();
      $checker->value = false;
      return $i;
    });

    /* HH_IGNORE_ERROR[4107] PHP stdlib */
    /* HH_IGNORE_ERROR[2049] PHP stdlib */
    $start = microtime(true);
    $results = await Vec\map_async(
      Vec\range(0, 9),
      async $i ==> await $semaphore->waitForAsync($i),
    );
    /* HH_IGNORE_ERROR[4107] PHP stdlib */
    /* HH_IGNORE_ERROR[2049] PHP stdlib */
    $end = microtime(true);
    expect($checker->value)->toBeFalse();
    expect($end - $start)->toEqualWithDelta(self::SLEEP_BLOCK * 10, self::TIME_DELTA);
    expect($results)->toEqual(Vec\range(0, 9));
  }

  public async function testExtreme1(): Awaitable<void> {
    $semaphore = new Async\Semaphore(1, async ($i) ==> $i);
    /* HH_IGNORE_ERROR[4107] PHP stdlib */
    /* HH_IGNORE_ERROR[2049] PHP stdlib */
    $start = microtime(true);
    $results = await Vec\map_async(
      Vec\range(0, 9999),
      async $i ==> await $semaphore->waitForAsync($i),
    );
    /* HH_IGNORE_ERROR[4107] PHP stdlib */
    /* HH_IGNORE_ERROR[2049] PHP stdlib */
    $end = microtime(true);
    expect($end - $start)->toEqualWithDelta(0., self::TIME_DELTA);
    expect($results)->toEqual(Vec\range(0, 9999));
  }

  public async function testExtreme2(): Awaitable<void> {
    $semaphore = new Async\Semaphore(10000, async ($i) ==> {
      await HH\Asio\usleep(self::USLEEP_BLOCK);
      return $i;
    });
    /* HH_IGNORE_ERROR[4107] PHP stdlib */
    /* HH_IGNORE_ERROR[2049] PHP stdlib */
    $start = microtime(true);
    $results = await Vec\map_async(
      Vec\range(0, 9999),
      async $i ==> await $semaphore->waitForAsync($i),
    );
    /* HH_IGNORE_ERROR[4107] PHP stdlib */
    /* HH_IGNORE_ERROR[2049] PHP stdlib */
    $end = microtime(true);
    expect($end - $start)->toEqualWithDelta(self::SLEEP_BLOCK, self::TIME_DELTA);
    expect($results)->toEqual(Vec\range(0, 9999));
  }

  public async function testExtreme3(): Awaitable<void> {
    $semaphore = new Async\Semaphore(1, async ($i) ==> {
      await stop_eager_execution();
      return $i;
    });
    /* HH_IGNORE_ERROR[4107] PHP stdlib */
    /* HH_IGNORE_ERROR[2049] PHP stdlib */
    $start = microtime(true);
    $results = await Vec\map_async(
      Vec\range(0, 9999),
      async $i ==> await $semaphore->waitForAsync($i),
    );
    /* HH_IGNORE_ERROR[4107] PHP stdlib */
    /* HH_IGNORE_ERROR[2049] PHP stdlib */
    $end = microtime(true);
    expect($end - $start)->toEqualWithDelta(0., self::TIME_DELTA);
    expect($results)->toEqual(Vec\range(0, 9999));
  }
}
