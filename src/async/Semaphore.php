<?hh // strict
/*
 *  Copyright (c) 2004-present, Facebook, Inc.
 *  All rights reserved.
 *
 *  This source code is licensed under the MIT license found in the
 *  LICENSE file in the root directory of this source tree.
 *
 */

namespace HH\Lib\Async;

use namespace HH\Lib\C;

final class Semaphore<Tin, Tout> {

  private static int $uniqueIDCounter = 0;
  private dict<int, Condition<null>> $blocking = dict[];
  private ?Awaitable<void> $activeGen;
  private int $runningCount = 0;
  private int $recentOpenCount = 0;

  public function __construct(
    private int $concurrentLimit,
    private (function(Tin): Awaitable<Tout>) $f,
  ) {
    invariant($concurrentLimit > 0, "Concurrent limit must be greater than 0.");
  }

  public async function waitForAsync(Tin $value): Awaitable<Tout> {
    $gen = async {
      if (
        $this->runningCount + $this->recentOpenCount >= $this->concurrentLimit
      ) {
        $unique_id = self::$uniqueIDCounter;
        self::$uniqueIDCounter++;
        $condition = new Condition();
        $this->blocking[$unique_id] = $condition;
        await $condition->waitForNotificationAsync(async {
          await $this->activeGen;
        });
        invariant(
          $this->recentOpenCount > 0,
          'Expecting at least one recentOpenCount.',
        );
        $this->recentOpenCount--;
      }
      invariant(
        $this->runningCount < $this->concurrentLimit,
        'Expecting open run slot',
      );
      $f = $this->f;
      $this->runningCount++;
      try {
        return await $f($value);
      } finally {
        $this->runningCount--;
        $next_blocked_id = C\first_key($this->blocking);
        if ($next_blocked_id !== null) {
          $next_blocked = $this->blocking[$next_blocked_id];
          unset($this->blocking[$next_blocked_id]);
          $this->recentOpenCount++;
          $next_blocked->succeed(null);
        }
      }
    };
    /* HH_FIXME[4110]: the types of $this->activeGen and $gen are both ruined
                       by being unified when passing them to fromVec */
    $this->activeGen ??= async {};
    $this->activeGen = AwaitAllWaitHandle::fromVec(vec[$this->activeGen, $gen]);
    /* HH_FIXME[4110]: the types of $this->activeGen and $gen are both ruined
                       by being unified when passing them to fromVec */
    return await $gen;
  }
}
